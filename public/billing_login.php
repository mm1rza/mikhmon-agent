<?php
session_start();

date_default_timezone_set('Asia/Jakarta');

require_once(__DIR__ . '/../include/db_config.php');
require_once(__DIR__ . '/../lib/BillingService.class.php');
require_once(__DIR__ . '/../lib/WhatsAppNotification.class.php');

if (isset($_SESSION['billing_portal_customer_id']) && (int)$_SESSION['billing_portal_customer_id'] > 0) {
    header('Location: billing_portal.php');
    exit;
}

$identifier = '';
$otpCodeInput = '';
$error = $_SESSION['billing_portal_flash_error'] ?? null;
$success = $_SESSION['billing_portal_flash_success'] ?? null;
unset($_SESSION['billing_portal_flash_error'], $_SESSION['billing_portal_flash_success']);

try {
    $billingService = new BillingService();
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Tidak dapat memuat layanan billing: ' . htmlspecialchars($e->getMessage());
    exit;
}

$contactHeading = $billingService->getSetting('billing_portal_contact_heading', 'Butuh bantuan? Hubungi Admin ISP');
$contactWhatsapp = $billingService->getSetting('billing_portal_contact_whatsapp', '08123456789');
$contactEmail = $billingService->getSetting('billing_portal_contact_email', 'support@ispanda.com');
$contactBody = $billingService->getSetting('billing_portal_contact_body', 'Jam operasional: 08.00 - 22.00');

$otpEnabled = $billingService->getSetting('billing_portal_otp_enabled', '0') === '1';
$otpDigits = (int)$billingService->getSetting('billing_portal_otp_digits', '6');
$otpExpiry = (int)$billingService->getSetting('billing_portal_otp_expiry_minutes', '5');
$otpMaxAttempts = (int)$billingService->getSetting('billing_portal_otp_max_attempts', '5');

$otpDigits = max(4, min(8, $otpDigits));
$otpExpiry = max(1, min(30, $otpExpiry));
$otpMaxAttempts = max(1, min(10, $otpMaxAttempts));

$pendingLogin = $_SESSION['billing_portal_pending_login'] ?? null;
$stage = 'identifier';
if ($otpEnabled && $pendingLogin && is_array($pendingLogin)) {
    $stage = 'otp';
    $identifier = $pendingLogin['identifier'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'request';

    if ($action === 'cancel_pending') {
        unset($_SESSION['billing_portal_pending_login']);
        $stage = 'identifier';
        $success = null;
    } elseif ($action === 'request_login') {
        $identifier = trim($_POST['identifier'] ?? '');

        if ($identifier === '') {
            $error = 'Mohon masukkan nomor telepon, ID layanan, atau PPPoE username.';
            $stage = 'identifier';
        } else {
            try {
                $customer = $billingService->findCustomerForPortal($identifier);

                if (!$customer) {
                    $error = 'Data pelanggan tidak ditemukan. Pastikan nomor telepon atau ID benar.';
                } elseif (!$otpEnabled) {
                    $_SESSION['billing_portal_customer_id'] = (int)$customer['id'];
                    $_SESSION['billing_portal_last_login'] = time();
                    header('Location: billing_portal.php');
                    exit;
                } else {
                    $phone = trim($customer['phone'] ?? '');
                    if ($phone === '') {
                        $error = 'Nomor WhatsApp pelanggan belum terisi. Hubungi admin untuk memperbarui data.';
                    } else {
                        $billingService->cleanupExpiredPortalOtps();
                        $otpCode = $billingService->createPortalOtp((int)$customer['id'], $identifier, $otpDigits, $otpExpiry, $otpMaxAttempts);

                        $expiresAt = (new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta')))->modify('+' . $otpExpiry . ' minutes');
                        $formattedExpiry = $expiresAt->format('d M Y H:i');

                        $messageLines = [];
                        $messageLines[] = '🔐 *Kode OTP Portal Pelanggan*';
                        $messageLines[] = '';
                        $messageLines[] = 'Kode OTP: *' . $otpCode . '*';
                        $messageLines[] = 'Berlaku sampai: ' . $formattedExpiry . ' WIB';
                        $messageLines[] = '';
                        $messageLines[] = 'Jangan bagikan kode ini kepada siapa pun.';
                        if (!empty($contactHeading)) {
                            $messageLines[] = '';
                            $messageLines[] = $contactHeading;
                        }
                        if (!empty($contactWhatsapp)) {
                            $messageLines[] = 'WA Admin: ' . $contactWhatsapp;
                        }
                        if (!empty($contactEmail)) {
                            $messageLines[] = 'Email: ' . $contactEmail;
                        }
                        if (!empty($contactBody)) {
                            $messageLines[] = $contactBody;
                        }

                        $notification = new WhatsAppNotification();
                        $waPayload = implode("\n", $messageLines);
                        $sent = $notification->sendPlainMessage($phone, $waPayload);

                        if ($sent) {
                            $_SESSION['billing_portal_pending_login'] = [
                                'customer_id' => (int)$customer['id'],
                                'identifier' => $identifier,
                                'phone' => $phone,
                                'name' => $customer['name'] ?? '',
                                'requested_at' => time(),
                            ];
                            $success = 'Kode OTP telah dikirim ke WhatsApp ' . htmlspecialchars($phone) . '. Masukkan kode untuk melanjutkan.';
                            $error = null;
                            $stage = 'otp';
                        } else {
                            $error = 'Gagal mengirim kode OTP. Pastikan gateway WhatsApp aktif dan nomor pelanggan valid.';
                            $stage = 'identifier';
                        }
                    }
                }
            } catch (Throwable $e) {
                $error = 'Terjadi kesalahan saat memproses login: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'verify_otp' && $otpEnabled) {
        $otpCodeInput = trim($_POST['otp_code'] ?? '');
        $pending = $_SESSION['billing_portal_pending_login'] ?? null;

        if (!$pending) {
            $error = 'Sesi OTP tidak ditemukan. Silakan masukkan kembali ID layanan.';
            $stage = 'identifier';
        } elseif ($otpCodeInput === '') {
            $error = 'Mohon masukkan kode OTP yang diterima.';
            $stage = 'otp';
        } else {
            $verify = $billingService->verifyPortalOtp((int)$pending['customer_id'], $pending['identifier'], $otpCodeInput);

            if ($verify['success']) {
                unset($_SESSION['billing_portal_pending_login']);
                $_SESSION['billing_portal_customer_id'] = (int)$pending['customer_id'];
                $_SESSION['billing_portal_last_login'] = time();
                header('Location: billing_portal.php');
                exit;
            } else {
                $error = $verify['message'] ?? 'Kode OTP tidak valid.';
                if (in_array($verify['reason'], ['expired', 'attempts_exceeded', 'not_found'], true)) {
                    unset($_SESSION['billing_portal_pending_login']);
                    $stage = 'identifier';
                } else {
                    $stage = 'otp';
                }
            }
        }
    }
}

$theme = 'default';
$themecolor = '#3a4149';
if (file_exists(__DIR__ . '/../include/theme.php')) {
    include(__DIR__ . '/../include/theme.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Billing Pelanggan</title>
    <meta name="theme-color" content="<?= $themecolor; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="../css/mikhmon-ui.<?= $theme; ?>.min.css">
    <link rel="icon" href="../img/favicon.png" />
    <style>
        body { background-color: #ecf0f5; font-family: 'Source Sans Pro', Arial, sans-serif; }
        .login-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #fff; border-radius: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); width: 100%; max-width: 420px; padding: 32px; }
        .login-card h1 { margin: 0 0 12px; font-size: 24px; color: #111827; font-weight: 600; }
        .login-card p.subtitle { margin: 0 0 24px; color: #6b7280; font-size: 14px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; transition: border-color 0.2s; }
        .form-control:focus { border-color: #3c8dbc; outline: none; box-shadow: 0 0 0 3px rgba(60,141,188,0.15); }
        .btn-primary { width: 100%; padding: 12px; border: none; border-radius: 6px; background: #3c8dbc; color: #fff; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { background: #3577a8; }
        .info-text { font-size: 13px; color: #6b7280; margin-top: 10px; line-height: 1.5; }
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 14px;
            border: 1px solid transparent;
            box-sizing: border-box;
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .alert strong { display: inline-block; min-width: 64px; }
        .alert-error { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <h1><i class="fa fa-lock"></i> Portal Pelanggan</h1>
        <p class="subtitle">Masukkan nomor telepon yang terdaftar di billing atau ID layanan lain (service number / PPPoE username).</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><strong>Gagal:</strong> <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><strong>Berhasil:</strong> <?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($stage === 'identifier'): ?>
            <form method="post" autocomplete="off">
                <input type="hidden" name="action" value="request_login">
                <div class="form-group">
                    <label for="identifier">Nomor Telepon / ID Layanan</label>
                    <input type="text" name="identifier" id="identifier" class="form-control" value="<?= htmlspecialchars($identifier); ?>" placeholder="Contoh: 6281234567890" required>
                </div>
                <button type="submit" class="btn-primary">Lanjutkan</button>
            </form>
        <?php elseif ($stage === 'otp'): ?>
            <form method="post" autocomplete="off">
                <input type="hidden" name="action" value="verify_otp">
                <div class="form-group">
                    <label for="otp_code">Kode OTP</label>
                    <input type="text" name="otp_code" id="otp_code" class="form-control" value="<?= htmlspecialchars($otpCodeInput); ?>" placeholder="Masukkan 6 digit OTP" required>
                </div>
                <button type="submit" class="btn-primary">Verifikasi OTP</button>
            </form>
            <form method="post" style="margin-top:10px;">
                <input type="hidden" name="action" value="cancel_pending">
                <button type="submit" class="btn btn-default" style="width:100%;">Ganti Nomor / ID Lain</button>
            </form>
        <?php endif; ?>

        <p class="info-text">
            <?= htmlspecialchars($contactHeading); ?><br>
            <?php if (!empty($contactWhatsapp)): ?>Telp/WhatsApp: <?= htmlspecialchars($contactWhatsapp); ?><br><?php endif; ?>
            <?php if (!empty($contactEmail)): ?>Email: <?= htmlspecialchars($contactEmail); ?><br><?php endif; ?>
            <?= nl2br(htmlspecialchars($contactBody)); ?>
        </p>
    </div>
</div>
</body>
</html>
