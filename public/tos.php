<?php
/*
 * Terms of Service (Syarat dan Ketentuan)
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
    <title>Syarat & Ketentuan - <?= htmlspecialchars($site_name); ?></title>
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
            border-bottom: 3px solid #3c8dbc;
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
            color: #3c8dbc;
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
            background: #f8f9fa;
            border-left: 4px solid #3c8dbc;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 3px;
        }
        
        .highlight-box strong {
            color: #3c8dbc;
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
            background: #3c8dbc;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #2c6d9c;
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
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1><i class="fa fa-file-text-o"></i> Syarat & Ketentuan</h1>
                <p class="subtitle">Terms of Service</p>
            </div>
            
            <div class="content">
                <p><strong>Terakhir diperbarui:</strong> <?= date('d F Y'); ?></p>
                
                <div class="highlight-box">
                    <strong>Penting:</strong> Dengan melakukan pembelian voucher WiFi melalui platform ini, Anda menyatakan telah membaca, memahami, dan menyetujui semua syarat dan ketentuan yang berlaku.
                </div>
                
                <h2>1. Definisi</h2>
                <p>Dalam syarat dan ketentuan ini:</p>
                <ul>
                    <li><strong>"Kami"</strong> atau <strong>"Penyedia"</strong> mengacu pada <?= htmlspecialchars($agent_name ?: 'penyedia layanan WiFi'); ?></li>
                    <li><strong>"Anda"</strong> atau <strong>"Pengguna"</strong> mengacu pada pembeli voucher WiFi</li>
                    <li><strong>"Voucher"</strong> mengacu pada kode akses internet yang dibeli melalui platform ini</li>
                    <li><strong>"Platform"</strong> mengacu pada sistem pembelian voucher online ini</li>
                </ul>
                
                <h2>2. Pembelian Voucher</h2>
                
                <h3>2.1 Proses Pembelian</h3>
                <ul>
                    <li>Pembelian voucher dilakukan secara online melalui platform ini</li>
                    <li>Pembayaran dapat dilakukan melalui berbagai metode yang tersedia (QRIS, Virtual Account, E-Wallet, dll)</li>
                    <li>Voucher akan dikirim otomatis ke WhatsApp yang terdaftar setelah pembayaran berhasil</li>
                    <li>Waktu pengiriman voucher: maksimal 5 menit setelah pembayaran dikonfirmasi</li>
                </ul>
                
                <h3>2.2 Harga dan Pembayaran</h3>
                <ul>
                    <li>Harga voucher sudah termasuk pajak yang berlaku</li>
                    <li>Biaya admin payment gateway ditanggung oleh pembeli</li>
                    <li>Harga dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya</li>
                    <li>Pembayaran yang sudah dilakukan tidak dapat dibatalkan</li>
                </ul>
                
                <h2>3. Penggunaan Voucher</h2>
                
                <h3>3.1 Ketentuan Umum</h3>
                <ul>
                    <li>Setiap voucher hanya dapat digunakan oleh satu perangkat pada satu waktu</li>
                    <li>Username dan password voucher bersifat pribadi dan tidak boleh dibagikan</li>
                    <li>Voucher mulai aktif sejak pertama kali digunakan untuk login</li>
                    <li>Masa berlaku voucher sesuai dengan paket yang dibeli</li>
                </ul>
                
                <h3>3.2 Batasan Penggunaan</h3>
                <ul>
                    <li>Voucher tidak dapat dipindahtangankan ke orang lain</li>
                    <li>Satu voucher hanya berlaku untuk satu sesi koneksi</li>
                    <li>Kuota atau waktu yang tidak terpakai tidak dapat dikembalikan atau dipindahkan</li>
                    <li>Voucher yang sudah expired tidak dapat diaktifkan kembali</li>
                </ul>
                
                <h2>4. Kebijakan Pengembalian Dana (Refund)</h2>
                
                <h3>4.1 Kondisi Refund</h3>
                <p>Pengembalian dana <strong>HANYA</strong> dapat dilakukan dalam kondisi berikut:</p>
                <ul>
                    <li>Voucher tidak diterima dalam waktu 24 jam setelah pembayaran berhasil</li>
                    <li>Voucher tidak dapat digunakan karena kesalahan sistem kami</li>
                    <li>Terjadi duplikasi pembayaran untuk voucher yang sama</li>
                </ul>
                
                <h3>4.2 Kondisi TIDAK Dapat Refund</h3>
                <ul>
                    <li>Voucher sudah pernah digunakan/diaktifkan (walaupun hanya 1 kali login)</li>
                    <li>Kesalahan input nomor WhatsApp oleh pembeli</li>
                    <li>Pembeli tidak puas dengan kecepatan internet</li>
                    <li>Voucher sudah melewati masa berlaku</li>
                    <li>Koneksi internet lambat karena gangguan provider/ISP</li>
                </ul>
                
                <h3>4.3 Proses Refund</h3>
                <ul>
                    <li>Pengajuan refund harus dilakukan maksimal 7x24 jam setelah pembelian</li>
                    <li>Refund akan diproses dalam waktu 3-7 hari kerja</li>
                    <li>Pengembalian dana akan dikurangi biaya admin payment gateway</li>
                    <li>Refund dilakukan ke metode pembayaran yang sama</li>
                </ul>
                
                <h2>5. Kewajiban Pengguna</h2>
                <p>Pengguna dilarang untuk:</p>
                <ul>
                    <li>Menggunakan layanan untuk aktivitas ilegal atau melanggar hukum</li>
                    <li>Melakukan hacking, cracking, atau aktivitas yang mengganggu sistem</li>
                    <li>Menyebarkan konten ilegal, pornografi, atau melanggar SARA</li>
                    <li>Melakukan spamming atau pengiriman email massal</li>
                    <li>Menggunakan bandwidth berlebihan yang mengganggu pengguna lain</li>
                    <li>Berbagi atau menjual kembali voucher tanpa izin</li>
                </ul>
                
                <h2>6. Pembatasan Layanan</h2>
                <p>Kami berhak untuk:</p>
                <ul>
                    <li>Memblokir atau menonaktifkan voucher yang melanggar ketentuan</li>
                    <li>Membatasi kecepatan jika terjadi penyalahgunaan bandwidth</li>
                    <li>Melakukan maintenance tanpa pemberitahuan sebelumnya</li>
                    <li>Mengubah atau menghentikan layanan sewaktu-waktu</li>
                    <li>Memblokir akses ke situs tertentu yang melanggar hukum</li>
                </ul>
                
                <h2>7. Privasi dan Keamanan Data</h2>
                <ul>
                    <li>Data pribadi Anda akan dijaga kerahasiaannya</li>
                    <li>Data hanya digunakan untuk keperluan transaksi dan layanan</li>
                    <li>Kami tidak akan membagikan data Anda kepada pihak ketiga tanpa izin</li>
                    <li>Lihat <a href="privacy.php<?= !empty($agent_code) ? '?agent='.$agent_code : ''; ?>">Kebijakan Privasi</a> untuk detail lengkap</li>
                </ul>
                
                <h2>8. Batasan Tanggung Jawab</h2>
                <p>Kami tidak bertanggung jawab atas:</p>
                <ul>
                    <li>Kerugian akibat kelalaian pengguna dalam menjaga kerahasiaan voucher</li>
                    <li>Gangguan layanan akibat force majeure (bencana alam, pemadaman listrik, dll)</li>
                    <li>Kehilangan data atau informasi akibat aktivitas pengguna</li>
                    <li>Kerugian tidak langsung, insidental, atau konsekuensial</li>
                    <li>Konten yang diakses pengguna melalui internet</li>
                </ul>
                
                <h2>9. Layanan Pelanggan</h2>
                <div class="highlight-box">
                    <p><strong>Untuk bantuan atau pertanyaan, hubungi:</strong></p>
                    <?php if ($agent_phone): ?>
                    <p><i class="fa fa-whatsapp"></i> WhatsApp: <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $agent_phone); ?>" target="_blank"><?= htmlspecialchars($agent_phone); ?></a></p>
                    <?php endif; ?>
                    <p><i class="fa fa-clock-o"></i> Jam operasional: 08:00 - 20:00 WIB</p>
                </div>
                
                <h2>10. Perubahan Syarat & Ketentuan</h2>
                <ul>
                    <li>Kami berhak mengubah syarat dan ketentuan ini sewaktu-waktu</li>
                    <li>Perubahan akan diberitahukan melalui platform ini</li>
                    <li>Penggunaan layanan setelah perubahan berarti Anda menyetujui perubahan tersebut</li>
                    <li>Disarankan untuk memeriksa halaman ini secara berkala</li>
                </ul>
                
                <h2>11. Hukum yang Berlaku</h2>
                <p>Syarat dan ketentuan ini diatur dan ditafsirkan sesuai dengan hukum Negara Republik Indonesia. Setiap perselisihan yang timbul akan diselesaikan melalui musyawarah atau melalui pengadilan yang berwenang.</p>
                
                <div class="highlight-box" style="margin-top: 40px;">
                    <p><strong>Dengan melakukan pembelian, Anda menyatakan bahwa:</strong></p>
                    <ul style="margin-bottom: 0;">
                        <li>Anda telah membaca dan memahami seluruh syarat dan ketentuan ini</li>
                        <li>Anda setuju untuk terikat dengan syarat dan ketentuan yang berlaku</li>
                        <li>Anda berusia minimal 17 tahun atau memiliki izin dari orang tua/wali</li>
                    </ul>
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
