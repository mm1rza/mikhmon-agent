<?php
/*
 * Privacy Policy (Kebijakan Privasi)
 * Public page
 */

// Get agent code from URL
$agent_code = $_GET['agent'] ?? $_GET['a'] ?? '';

include_once('../include/db_config.php');

// Get theme
$theme = 'default';
$themecolor = '#3a4149';
if (file_exists('../include/theme.php')) {
    include_once('../include/theme.php');
}

$site_name = 'WiFi Voucher';
$agent_name = '';
$agent_phone = '';

if (!empty($agent_code)) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT agent_name, phone FROM agents WHERE agent_code = :code AND status = 'active'");
        $stmt->execute([':code' => $agent_code]);
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($agent) {
            $site_name = $agent['agent_name'];
            $agent_name = $agent['agent_name'];
            $agent_phone = $agent['phone'] ?? '';
        }
    } catch (Exception $e) {
        // Use default
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - <?= htmlspecialchars($site_name); ?></title>
    <meta name="theme-color" content="<?= $themecolor; ?>" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="../css/font-awesome/css/font-awesome.min.css" />
    <!-- Mikhmon UI -->
    <link rel="stylesheet" href="../css/mikhmon-ui.<?= $theme; ?>.min.css">
    
    <style>
        body {
            background-color: #ecf0f5;
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
        }
        
        .wrapper {
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 3px;
            box-shadow: 0 1px 1px rgba(0,0,0,.1);
        }
        
        .header {
            padding: 30px;
            border-bottom: 3px solid #00a65a;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #3a4149;
            margin: 0 0 10px 0;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 1rem;
        }
        
        .content {
            padding: 40px;
        }
        
        .content h2 {
            color: #00a65a;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f5;
        }
        
        .content h2:first-child {
            margin-top: 0;
        }
        
        .content h3 {
            color: #3a4149;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 20px 0 10px 0;
        }
        
        .content p {
            margin-bottom: 15px;
            color: #444;
        }
        
        .content ul, .content ol {
            margin-bottom: 20px;
            padding-left: 30px;
        }
        
        .content li {
            margin-bottom: 10px;
            color: #444;
        }
        
        .highlight-box {
            background: #f0f9f4;
            border-left: 4px solid #00a65a;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 3px;
        }
        
        .highlight-box strong {
            color: #00a65a;
        }
        
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 3px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #3a4149;
        }
        
        .footer-nav {
            padding: 20px 40px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }
        
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: #00a65a;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #008d4c;
            color: white;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .content {
                padding: 20px;
            }
            
            .content h2 {
                font-size: 1.3rem;
            }
            
            .footer-nav {
                padding: 15px 20px;
            }
            
            .data-table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1><i class="fa fa-shield"></i> Kebijakan Privasi</h1>
                <p class="subtitle">Privacy Policy</p>
            </div>
            
            <div class="content">
                <p><strong>Terakhir diperbarui:</strong> <?= date('d F Y'); ?></p>
                
                <div class="highlight-box">
                    <p><strong><i class="fa fa-lock"></i> Komitmen Kami:</strong></p>
                    <p style="margin-bottom: 0;">Privasi Anda adalah prioritas kami. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.</p>
                </div>
                
                <h2>1. Informasi yang Kami Kumpulkan</h2>
                
                <h3>1.1 Data Pribadi</h3>
                <p>Saat Anda membeli voucher WiFi, kami mengumpulkan:</p>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Jenis Data</th>
                            <th>Contoh</th>
                            <th>Tujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Nama Lengkap</strong></td>
                            <td>John Doe</td>
                            <td>Identifikasi pelanggan</td>
                        </tr>
                        <tr>
                            <td><strong>Nomor WhatsApp</strong></td>
                            <td>08123456789</td>
                            <td>Pengiriman voucher</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>user@example.com</td>
                            <td>Konfirmasi transaksi (opsional)</td>
                        </tr>
                        <tr>
                            <td><strong>Metode Pembayaran</strong></td>
                            <td>QRIS, VA, E-Wallet</td>
                            <td>Proses pembayaran</td>
                        </tr>
                    </tbody>
                </table>
                
                <h3>1.2 Data Teknis</h3>
                <ul>
                    <li><strong>IP Address:</strong> Untuk keamanan dan pencegahan fraud</li>
                    <li><strong>Device Information:</strong> Jenis perangkat, browser, OS</li>
                    <li><strong>Log Koneksi:</strong> Waktu login, logout, bandwidth usage</li>
                    <li><strong>Transaction ID:</strong> Untuk tracking dan customer support</li>
                </ul>
                
                <h3>1.3 Data yang TIDAK Kami Kumpulkan</h3>
                <div class="info-box">
                    <p><strong><i class="fa fa-info-circle"></i> Penting:</strong></p>
                    <ul style="margin-bottom: 0;">
                        <li>Kami <strong>TIDAK</strong> menyimpan history browsing Anda</li>
                        <li>Kami <strong>TIDAK</strong> melacak situs web yang Anda kunjungi</li>
                        <li>Kami <strong>TIDAK</strong> mengakses konten komunikasi pribadi Anda</li>
                        <li>Kami <strong>TIDAK</strong> menjual data pribadi Anda kepada pihak ketiga</li>
                    </ul>
                </div>
                
                <h2>2. Bagaimana Kami Menggunakan Data Anda</h2>
                
                <h3>2.1 Tujuan Penggunaan</h3>
                <p>Data yang kami kumpulkan digunakan untuk:</p>
                <ul>
                    <li><i class="fa fa-check text-success"></i> <strong>Proses Transaksi:</strong> Memproses pembelian dan pembayaran voucher</li>
                    <li><i class="fa fa-check text-success"></i> <strong>Pengiriman Voucher:</strong> Mengirim kode voucher ke WhatsApp Anda</li>
                    <li><i class="fa fa-check text-success"></i> <strong>Customer Support:</strong> Menanggapi pertanyaan dan keluhan</li>
                    <li><i class="fa fa-check text-success"></i> <strong>Keamanan:</strong> Mencegah fraud dan aktivitas mencurigakan</li>
                    <li><i class="fa fa-check text-success"></i> <strong>Perbaikan Layanan:</strong> Analisis untuk meningkatkan kualitas layanan</li>
                    <li><i class="fa fa-check text-success"></i> <strong>Compliance:</strong> Memenuhi kewajiban hukum yang berlaku</li>
                </ul>
                
                <h3>2.2 Automated Decision Making</h3>
                <p>Kami menggunakan sistem otomatis untuk:</p>
                <ul>
                    <li>Verifikasi pembayaran</li>
                    <li>Pengiriman voucher otomatis</li>
                    <li>Deteksi fraud dan abuse</li>
                    <li>Blocking otomatis untuk aktivitas mencurigakan</li>
                </ul>
                
                <h2>3. Berbagi Data dengan Pihak Ketiga</h2>
                
                <h3>3.1 Payment Gateway</h3>
                <p>Kami berbagi data dengan payment gateway partner untuk proses pembayaran:</p>
                <ul>
                    <li><strong>Tripay:</strong> Untuk VA, QRIS, E-Wallet, dan Retail</li>
                    <li><strong>Xendit:</strong> Untuk berbagai metode pembayaran</li>
                    <li><strong>Midtrans:</strong> Untuk gateway pembayaran</li>
                </ul>
                <p><em>Data yang dibagikan: Nama, nomor telepon, email, jumlah pembayaran</em></p>
                
                <h3>3.2 WhatsApp Business API</h3>
                <p>Nomor WhatsApp Anda digunakan untuk:</p>
                <ul>
                    <li>Pengiriman kode voucher</li>
                    <li>Notifikasi status pembayaran</li>
                    <li>Customer support dan komunikasi layanan</li>
                </ul>
                
                <h3>3.3 Data yang TIDAK Dibagikan</h3>
                <p>Kami <strong>TIDAK PERNAH</strong> membagikan data Anda untuk:</p>
                <ul>
                    <li>❌ Marketing pihak ketiga</li>
                    <li>❌ Penjualan database pelanggan</li>
                    <li>❌ Spam atau telemarketing</li>
                    <li>❌ Tujuan komersial di luar layanan kami</li>
                </ul>
                
                <h2>4. Keamanan Data</h2>
                
                <h3>4.1 Langkah Pengamanan</h3>
                <p>Kami menerapkan berbagai tindakan keamanan:</p>
                <ul>
                    <li><i class="fa fa-lock"></i> <strong>Enkripsi SSL/TLS:</strong> Semua data ditransmisikan dengan enkripsi</li>
                    <li><i class="fa fa-database"></i> <strong>Database Security:</strong> Akses database dibatasi dan terenkripsi</li>
                    <li><i class="fa fa-shield"></i> <strong>Firewall:</strong> Proteksi terhadap akses tidak sah</li>
                    <li><i class="fa fa-user-secret"></i> <strong>Access Control:</strong> Hanya staff authorized yang dapat akses data</li>
                    <li><i class="fa fa-history"></i> <strong>Audit Logs:</strong> Semua akses data tercatat dan dimonitor</li>
                </ul>
                
                <h3>4.2 Data Retention (Penyimpanan Data)</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Jenis Data</th>
                            <th>Durasi Penyimpanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Data Transaksi</td>
                            <td>5 tahun (keperluan akuntansi & pajak)</td>
                        </tr>
                        <tr>
                            <td>Log Koneksi</td>
                            <td>3 bulan</td>
                        </tr>
                        <tr>
                            <td>Customer Support Tickets</td>
                            <td>1 tahun</td>
                        </tr>
                        <tr>
                            <td>Voucher Codes (inactive)</td>
                            <td>1 tahun</td>
                        </tr>
                    </tbody>
                </table>
                
                <h2>5. Hak-Hak Anda</h2>
                
                <p>Sebagai pengguna, Anda memiliki hak untuk:</p>
                
                <h3>5.1 Akses Data</h3>
                <ul>
                    <li><i class="fa fa-search"></i> <strong>Melihat Data:</strong> Meminta salinan data pribadi yang kami simpan</li>
                    <li><i class="fa fa-edit"></i> <strong>Koreksi Data:</strong> Memperbaiki data yang tidak akurat</li>
                    <li><i class="fa fa-download"></i> <strong>Portabilitas:</strong> Mendapatkan data Anda dalam format yang dapat dibaca</li>
                </ul>
                
                <h3>5.2 Penghapusan Data</h3>
                <ul>
                    <li><i class="fa fa-trash"></i> <strong>Hak untuk Dilupakan:</strong> Meminta penghapusan data pribadi</li>
                    <li><i class="fa fa-ban"></i> <strong>Pembatasan:</strong> Membatasi pemrosesan data pribadi Anda</li>
                </ul>
                
                <p><strong>Catatan:</strong> Beberapa data mungkin tidak dapat dihapus karena kewajiban hukum (misalnya data transaksi untuk keperluan pajak).</p>
                
                <h3>5.3 Cara Menggunakan Hak Anda</h3>
                <div class="highlight-box">
                    <p><strong>Untuk mengajukan permintaan terkait data pribadi:</strong></p>
                    <?php if ($agent_phone): ?>
                    <p><i class="fa fa-whatsapp"></i> Hubungi kami via WhatsApp: <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $agent_phone); ?>" target="_blank"><?= htmlspecialchars($agent_phone); ?></a></p>
                    <?php endif; ?>
                    <p style="margin-bottom: 0;"><i class="fa fa-clock-o"></i> Kami akan merespon dalam waktu maksimal 14 hari kerja</p>
                </div>
                
                <h2>6. Cookies dan Tracking</h2>
                
                <h3>6.1 Penggunaan Cookies</h3>
                <p>Website kami menggunakan cookies untuk:</p>
                <ul>
                    <li><strong>Essential Cookies:</strong> Untuk fungsi dasar website (login session, cart)</li>
                    <li><strong>Analytics:</strong> Untuk memahami penggunaan website (opsional)</li>
                </ul>
                
                <h3>6.2 Kontrol Cookies</h3>
                <p>Anda dapat mengontrol cookies melalui pengaturan browser Anda. Namun, menonaktifkan cookies tertentu dapat mempengaruhi fungsi website.</p>
                
                <h2>7. Privasi Anak</h2>
                <p>Layanan kami tidak ditujukan untuk anak di bawah 17 tahun. Kami tidak secara sengaja mengumpulkan data pribadi dari anak-anak tanpa persetujuan orang tua/wali.</p>
                
                <h2>8. Perubahan Kebijakan Privasi</h2>
                <ul>
                    <li>Kami dapat memperbarui kebijakan privasi ini sewaktu-waktu</li>
                    <li>Perubahan signifikan akan diberitahukan melalui email atau notifikasi di platform</li>
                    <li>Tanggal "Terakhir diperbarui" akan selalu menunjukkan versi terbaru</li>
                    <li>Penggunaan layanan setelah perubahan berarti Anda menyetujui kebijakan yang baru</li>
                </ul>
                
                <h2>9. Compliance & Regulasi</h2>
                <p>Kebijakan privasi ini sesuai dengan:</p>
                <ul>
                    <li><strong>UU ITE No. 19 Tahun 2016:</strong> Tentang Informasi dan Transaksi Elektronik</li>
                    <li><strong>UU PDP (Pelindungan Data Pribadi):</strong> Regulasi perlindungan data Indonesia</li>
                    <li><strong>Permenkominfo:</strong> Tentang perlindungan data pribadi sistem elektronik</li>
                </ul>
                
                <h2>10. Kontak Kami</h2>
                <div class="highlight-box">
                    <p><strong>Jika Anda memiliki pertanyaan tentang kebijakan privasi ini:</strong></p>
                    <p><i class="fa fa-building"></i> <strong><?= htmlspecialchars($agent_name ?: 'Penyedia Layanan'); ?></strong></p>
                    <?php if ($agent_phone): ?>
                    <p><i class="fa fa-whatsapp"></i> WhatsApp: <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $agent_phone); ?>" target="_blank"><?= htmlspecialchars($agent_phone); ?></a></p>
                    <?php endif; ?>
                    <p style="margin-bottom: 0;"><i class="fa fa-clock-o"></i> Jam operasional: 08:00 - 20:00 WIB</p>
                </div>
                
                <div class="info-box" style="margin-top: 40px;">
                    <p><strong><i class="fa fa-info-circle"></i> Terima kasih telah mempercayai kami!</strong></p>
                    <p style="margin-bottom: 0;">Privasi dan keamanan data Anda adalah prioritas utama kami. Kami berkomitmen untuk melindungi informasi pribadi Anda dengan standar keamanan terbaik.</p>
                </div>
            </div>
            
            <div class="footer-nav">
                <?php if (!empty($agent_code)): ?>
                <a href="index.php?agent=<?= htmlspecialchars($agent_code); ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Kembali ke Beranda
                </a>
                <?php else: ?>
                <a href="javascript:history.back()" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
