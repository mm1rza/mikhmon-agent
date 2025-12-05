<?php
/*
 * Telegram Bot Settings - Admin Panel
 * Configure Telegram Bot integration
 */

session_start();
require_once('../include/db_config.php');
require_once('../include/telegram_config.php');

// Check admin authentication (reuse your existing auth system)
// require_once('../check_admin.php');

$db = getDBConnection();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_settings'])) {
        try {
            $settings = [
                'telegram_enabled' => isset($_POST['telegram_enabled']) ? '1' : '0',
                'telegram_bot_token' => trim($_POST['telegram_bot_token']),
                'telegram_webhook_mode' => isset($_POST['telegram_webhook_mode']) ? '1' : '0',
                'telegram_admin_chat_ids' => trim($_POST['telegram_admin_chat_ids']),
                'telegram_welcome_message' => trim($_POST['telegram_welcome_message'])
            ];
            
            $stmt = $db->prepare("INSERT INTO telegram_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            
            foreach ($settings as $key => $value) {
                $stmt->execute([$key, $value, $value]);
            }
            
            $message = 'Settings saved successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error saving settings: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    if (isset($_POST['set_webhook'])) {
        $result = setTelegramWebhook();
        if (isset($result['ok']) && $result['ok']) {
            $message = 'Webhook set successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error setting webhook: ' . ($result['description'] ?? 'Unknown error');
            $messageType = 'error';
        }
    }
    
    if (isset($_POST['delete_webhook'])) {
        $result = deleteTelegramWebhook();
        if (isset($result['ok']) && $result['ok']) {
            $message = 'Webhook deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error deleting webhook: ' . ($result['description'] ?? 'Unknown error');
            $messageType = 'error';
        }
    }
    
    if (isset($_POST['test_connection'])) {
        $result = getTelegramBotInfo();
        if (isset($result['ok']) && $result['ok']) {
            $botInfo = $result['result'];
            $message = 'Connection successful! Bot: @' . $botInfo['username'] . ' (' . $botInfo['first_name'] . ')';
            $messageType = 'success';
        } else {
            $message = 'Connection failed: ' . ($result['description'] ?? 'Unknown error');
            $messageType = 'error';
        }
    }
}

// Load current settings
$settings = [];
try {
    $stmt = $db->query("SELECT setting_key, setting_value FROM telegram_settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    $message = 'Error loading settings: ' . $e->getMessage();
    $messageType = 'error';
}

// Get webhook info
$webhookInfo = getTelegramWebhookInfo();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Bot Settings - MikhMon</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        h2 {
            color: #374151;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        
        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #4f46e5;
            color: white;
        }
        
        .btn-primary:hover {
            background: #4338ca;
        }
        
        .btn-success {
            background: #10b981;
            color: white;
        }
        
        .btn-success:hover {
            background: #059669;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-top: 10px;
        }
        
        .info-box code {
            background: #1f2937;
            color: #10b981;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .help-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1><i class="fab fa-telegram"></i> Telegram Bot Settings</h1>
            <p class="subtitle">Configure Telegram Bot integration for MikhMon Agent System</p>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <div><?= htmlspecialchars($message) ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <h2>Basic Settings</h2>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="telegram_enabled" id="telegram_enabled" <?= isset($settings['telegram_enabled']) && $settings['telegram_enabled'] == '1' ? 'checked' : '' ?>>
                        <label for="telegram_enabled" style="margin: 0;">Enable Telegram Bot</label>
                    </div>
                    <p class="help-text">Aktifkan atau nonaktifkan bot Telegram</p>
                </div>
                
                <div class="form-group">
                    <label for="telegram_bot_token">Bot Token</label>
                    <input type="text" name="telegram_bot_token" id="telegram_bot_token" value="<?= htmlspecialchars($settings['telegram_bot_token'] ?? '') ?>" placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                    <p class="help-text">Dapatkan token dari @BotFather di Telegram</p>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="telegram_webhook_mode" id="telegram_webhook_mode" <?= isset($settings['telegram_webhook_mode']) && $settings['telegram_webhook_mode'] == '1' ? 'checked' : '' ?>>
                        <label for="telegram_webhook_mode" style="margin: 0;">Use Webhook Mode</label>
                    </div>
                    <p class="help-text">Webhook (butuh HTTPS) atau Long Polling</p>
                </div>
                
                <div class="form-group">
                    <label for="telegram_admin_chat_ids">Admin Chat IDs</label>
                    <input type="text" name="telegram_admin_chat_ids" id="telegram_admin_chat_ids" value="<?= htmlspecialchars($settings['telegram_admin_chat_ids'] ?? '') ?>" placeholder="123456789, 987654321">
                    <p class="help-text">Chat ID admin (pisahkan dengan koma). Dapatkan dari @userinfobot</p>
                </div>
                
                <div class="form-group">
                    <label for="telegram_welcome_message">Welcome Message</label>
                    <textarea name="telegram_welcome_message" id="telegram_welcome_message" placeholder="Selamat datang..."><?= htmlspecialchars($settings['telegram_welcome_message'] ?? '') ?></textarea>
                    <p class="help-text">Pesan sambutan saat user mengirim /start. Gunakan {name} untuk nama user.</p>
                </div>
                
                <div class="btn-group">
                    <button type="submit" name="save_settings" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                    <button type="submit" name="test_connection" class="btn btn-secondary">
                        <i class="fas fa-plug"></i> Test Connection
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>Webhook Management</h2>
            
            <?php if (isset($webhookInfo['ok']) && $webhookInfo['ok']): ?>
                <div class="form-group">
                    <label>Webhook Status</label>
                    <?php if (!empty($webhookInfo['result']['url'])): ?>
                        <span class="status-badge status-active">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                        <div class="info-box">
                            <strong>URL:</strong> <code><?= htmlspecialchars($webhookInfo['result']['url']) ?></code><br>
                            <strong>Pending Updates:</strong> <?= $webhookInfo['result']['pending_update_count'] ?? 0 ?>
                        </div>
                    <?php else: ?>
                        <span class="status-badge status-inactive">
                            <i class="fas fa-times-circle"></i> Not Set
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <strong>Webhook URL:</strong><br>
                <code><?= 'https://' . $_SERVER['HTTP_HOST'] . '/api/telegram_webhook.php' ?></code>
            </div>
            
            <form method="POST" style="margin-top: 20px;">
                <div class="btn-group">
                    <button type="submit" name="set_webhook" class="btn btn-success">
                        <i class="fas fa-link"></i> Set Webhook
                    </button>
                    <button type="submit" name="delete_webhook" class="btn btn-danger">
                        <i class="fas fa-unlink"></i> Delete Webhook
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>Setup Instructions</h2>
            <ol style="line-height: 1.8; color: #374151;">
                <li>Buka Telegram dan cari <strong>@BotFather</strong></li>
                <li>Kirim perintah <code>/newbot</code> untuk membuat bot baru</li>
                <li>Ikuti instruksi dan salin <strong>Bot Token</strong> yang diberikan</li>
                <li>Paste token di form di atas dan klik <strong>Save Settings</strong></li>
                <li>Klik <strong>Test Connection</strong> untuk memastikan token valid</li>
                <li>Klik <strong>Set Webhook</strong> untuk mengaktifkan webhook</li>
                <li>Cari bot Anda di Telegram dan kirim <code>/start</code></li>
            </ol>
            
            <div class="info-box" style="margin-top: 20px;">
                <strong>Note:</strong> Webhook memerlukan HTTPS. Pastikan server Anda sudah memiliki SSL certificate.
            </div>
        </div>
    </div>
</body>
</html>
