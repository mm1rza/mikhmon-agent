<?php
/*
 * FAQ (Frequently Asked Questions)
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
    <title>FAQ - <?= htmlspecialchars($site_name); ?></title>
    <meta name="theme-color" content="<?= $themecolor; ?>" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="../css/font-awesome/css/font-awesome.min.css" />
    <!-- Mikhmon UI -->
    <link rel="stylesheet" href="../css/mikhmon-ui.<?= $theme; ?>.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
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
            border-bottom: 3px solid #f39c12;
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
        
        .faq-category {
            margin-bottom: 40px;
        }
        
        .category-title {
            color: #f39c12;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 0 20px 0;
            padding: 15px;
            background: #fffbf0;
            border-left: 4px solid #f39c12;
            border-radius: 3px;
        }
        
        .category-title i {
            margin-right: 10px;
        }
        
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .faq-question {
            background: #f8f9fa;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            font-weight: 600;
            color: #3a4149;
        }
        
        .faq-question:hover {
            background: #e9ecef;
        }
        
        .faq-question.active {
            background: #f39c12;
            color: white;
        }
        
        .faq-question .icon {
            transition: transform 0.3s;
            font-size: 1.2rem;
        }
        
        .faq-question.active .icon {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 20px;
            display: none;
            background: white;
            color: #444;
            line-height: 1.8;
        }
        
        .faq-answer.show {
            display: block;
        }
        
        .faq-answer ul,
        .faq-answer ol {
            padding-left: 25px;
            margin: 10px 0;
        }
        
        .faq-answer li {
            margin-bottom: 8px;
        }
        
        .faq-answer strong {
            color: #f39c12;
        }
        
        .tip-box {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 3px;
        }
        
        .tip-box strong {
            color: #0c5460;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #856404;
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 3px;
        }
        
        .warning-box strong {
            color: #856404;
        }
        
        .search-box {
            margin-bottom: 30px;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #dee2e6;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #f39c12;
            box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
        }
        
        .search-box .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2rem;
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
            background: #f39c12;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #e08e0b;
            color: white;
        }
        
        .contact-cta {
            background: linear-gradient(135deg, #f39c12 0%, #e08e0b 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin-top: 40px;
        }
        
        .contact-cta h3 {
            margin: 0 0 15px 0;
            font-size: 1.5rem;
        }
        
        .contact-cta p {
            margin: 0 0 20px 0;
            opacity: 0.9;
        }
        
        .contact-cta a {
            display: inline-block;
            padding: 12px 30px;
            background: white;
            color: #f39c12;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .contact-cta a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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
            
            .category-title {
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
                <h1><i class="fa fa-question-circle"></i> Frequently Asked Questions</h1>
                <p class="subtitle">Pertanyaan yang Sering Diajukan</p>
            </div>
            
            <div class="content">
                <div class="search-box">
                    <input type="text" id="faqSearch" placeholder="Cari pertanyaan... (contoh: cara membeli, pembayaran, refund)">
                    <i class="fa fa-search search-icon"></i>
                </div>
                
                <!-- CATEGORY 1: PEMBELIAN & PEMBAYARAN -->
                <div class="faq-category">
                    <h2 class="category-title">
                        <i class="fa fa-shopping-cart"></i> Pembelian & Pembayaran
                    </h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Bagaimana cara membeli voucher WiFi?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Membeli voucher sangat mudah! Ikuti langkah berikut:</p>
                            <ol>
                                <li><strong>Buka halaman pembelian</strong> melalui link yang diberikan</li>
                                <li><strong>Pilih paket</strong> voucher sesuai kebutuhan Anda</li>
                                <li><strong>Isi data:</strong> Nama lengkap dan nomor WhatsApp</li>
                                <li><strong>Pilih metode pembayaran</strong> (QRIS, VA, E-Wallet, dll)</li>
                                <li><strong>Selesaikan pembayaran</strong></li>
                                <li><strong>Voucher otomatis dikirim</strong> ke WhatsApp Anda dalam 5 menit</li>
                            </ol>
                            <div class="tip-box">
                                <strong><i class="fa fa-lightbulb-o"></i> Tips:</strong> Pastikan nomor WhatsApp yang Anda masukkan aktif dan bisa menerima pesan!
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Metode pembayaran apa saja yang tersedia?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Kami menyediakan berbagai metode pembayaran untuk kemudahan Anda:</p>
                            <ul>
                                <li><strong>QRIS:</strong> Scan QR dengan berbagai e-wallet (GoPay, OVO, Dana, dll)</li>
                                <li><strong>Virtual Account:</strong> BCA, BNI, BRI, Mandiri, Permata, dll</li>
                                <li><strong>E-Wallet:</strong> GoPay, OVO, Dana, ShopeePay, LinkAja</li>
                                <li><strong>Retail:</strong> Alfamart, Indomaret</li>
                                <li><strong>Kartu Kredit/Debit:</strong> Visa, Mastercard (jika tersedia)</li>
                            </ul>
                            <p><em>Setiap metode pembayaran mungkin dikenakan biaya admin yang berbeda.</em></p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Berapa lama voucher dikirim setelah pembayaran?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Voucher dikirim <strong>otomatis</strong> ke WhatsApp Anda:</p>
                            <ul>
                                <li><i class="fa fa-check text-success"></i> <strong>QRIS & E-Wallet:</strong> 1-5 menit setelah pembayaran</li>
                                <li><i class="fa fa-check text-success"></i> <strong>Virtual Account:</strong> 5-15 menit setelah transfer</li>
                                <li><i class="fa fa-check text-success"></i> <strong>Retail (Alfamart/Indomaret):</strong> 5-30 menit setelah konfirmasi</li>
                            </ul>
                            <div class="warning-box">
                                <strong><i class="fa fa-exclamation-triangle"></i> Catatan:</strong> Jika voucher belum diterima dalam 1 jam, segera hubungi customer service kami dengan menyertakan bukti pembayaran.
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Apakah ada biaya admin?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Ya, biaya admin berbeda untuk setiap metode pembayaran:</p>
                            <ul>
                                <li><strong>QRIS:</strong> Biasanya 0.7% dari total transaksi</li>
                                <li><strong>Virtual Account:</strong> Rp 2.500 - Rp 4.000</li>
                                <li><strong>E-Wallet:</strong> Gratis hingga 1.5%</li>
                                <li><strong>Retail:</strong> Rp 2.500 - Rp 3.000</li>
                            </ul>
                            <p><em>Biaya admin sudah ditampilkan di halaman pembayaran sebelum Anda konfirmasi.</em></p>
                        </div>
                    </div>
                </div>
                
                <!-- CATEGORY 2: PENGGUNAAN VOUCHER -->
                <div class="faq-category">
                    <h2 class="category-title">
                        <i class="fa fa-wifi"></i> Penggunaan Voucher
                    </h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Bagaimana cara menggunakan voucher yang sudah dibeli?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Cara menggunakan voucher WiFi:</p>
                            <ol>
                                <li><strong>Hubungkan</strong> perangkat Anda ke jaringan WiFi</li>
                                <li><strong>Buka browser</strong> (Chrome, Firefox, Safari, dll)</li>
                                <li>Halaman login akan <strong>muncul otomatis</strong></li>
                                <li><strong>Masukkan Username dan Password</strong> dari voucher</li>
                                <li>Klik <strong>Login</strong></li>
                                <li>Anda sudah terhubung ke internet! 🎉</li>
                            </ol>
                            <div class="tip-box">
                                <strong><i class="fa fa-lightbulb-o"></i> Cara Cepat:</strong> Gunakan link login yang dikirim di WhatsApp untuk langsung masuk tanpa input manual!
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Berapa lama voucher berlaku?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Masa berlaku voucher tergantung paket yang Anda beli:</p>
                            <ul>
                                <li><strong>Paket Jam:</strong> 1 - 12 jam (dihitung dari login pertama)</li>
                                <li><strong>Paket Harian:</strong> 1 - 7 hari (24 jam x jumlah hari)</li>
                                <li><strong>Paket Bulanan:</strong> 30 hari</li>
                            </ul>
                            <div class="warning-box">
                                <strong><i class="fa fa-exclamation-triangle"></i> Penting:</strong>
                                <ul style="margin-top: 10px; margin-bottom: 0;">
                                    <li>Masa aktif mulai sejak <strong>login pertama kali</strong>, bukan dari pembelian</li>
                                    <li>Voucher yang sudah expired tidak dapat diperpanjang</li>
                                    <li>Waktu yang tidak terpakai tidak dapat dikembalikan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Apakah voucher bisa digunakan di beberapa perangkat?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p><strong>Tidak.</strong> Satu voucher hanya dapat digunakan untuk <strong>satu perangkat</strong> pada <strong>satu waktu</strong>.</p>
                            <p>Jika Anda login di perangkat lain dengan voucher yang sama, perangkat sebelumnya akan otomatis terputus.</p>
                            <div class="tip-box">
                                <strong><i class="fa fa-lightbulb-o"></i> Solusi:</strong> Jika Anda memiliki banyak perangkat, belilah voucher terpisah untuk setiap perangkat atau gunakan fitur hotspot/tethering dari satu perangkat.
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Kenapa saya tidak bisa login atau koneksi terputus?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Beberapa penyebab umum dan solusinya:</p>
                            <ol>
                                <li>
                                    <strong>Username/Password salah:</strong>
                                    <ul>
                                        <li>Pastikan tidak ada spasi di awal/akhir</li>
                                        <li>Perhatikan huruf besar/kecil (case-sensitive)</li>
                                        <li>Copy-paste dari pesan WhatsApp untuk akurasi</li>
                                    </ul>
                                </li>
                                <li>
                                    <strong>Voucher sudah expired:</strong>
                                    <ul>
                                        <li>Cek masa berlaku voucher Anda</li>
                                        <li>Voucher expired tidak dapat digunakan lagi</li>
                                    </ul>
                                </li>
                                <li>
                                    <strong>Voucher sedang digunakan perangkat lain:</strong>
                                    <ul>
                                        <li>Logout dari perangkat lain terlebih dahulu</li>
                                        <li>Tunggu 2-3 menit lalu coba lagi</li>
                                    </ul>
                                </li>
                                <li>
                                    <strong>Masalah jaringan WiFi:</strong>
                                    <ul>
                                        <li>Restart WiFi Anda (OFF/ON)</li>
                                        <li>Forget network dan reconnect</li>
                                        <li>Coba pindah lokasi lebih dekat ke access point</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <!-- CATEGORY 3: REFUND & KOMPLAIN -->
                <div class="faq-category">
                    <h2 class="category-title">
                        <i class="fa fa-money"></i> Refund & Pengembalian Dana
                    </h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Apakah voucher bisa dikembalikan (refund)?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Refund <strong>HANYA</strong> dapat dilakukan dalam kondisi sangat terbatas:</p>
                            
                            <p><strong style="color: #00a65a;"><i class="fa fa-check"></i> Kondisi DAPAT Refund:</strong></p>
                            <ul>
                                <li>Voucher tidak diterima dalam 24 jam setelah pembayaran berhasil</li>
                                <li>Voucher error/tidak bisa login karena kesalahan sistem kami</li>
                                <li>Double payment untuk transaksi yang sama</li>
                            </ul>
                            
                            <p><strong style="color: #dd4b39;"><i class="fa fa-times"></i> Kondisi TIDAK DAPAT Refund:</strong></p>
                            <ul>
                                <li>Voucher sudah pernah digunakan/login (walaupun cuma sekali)</li>
                                <li>Salah input nomor WhatsApp sendiri</li>
                                <li>Tidak puas dengan kecepatan internet</li>
                                <li>Voucher sudah expired</li>
                                <li>Berubah pikiran setelah membeli</li>
                            </ul>
                            
                            <div class="warning-box">
                                <strong><i class="fa fa-exclamation-triangle"></i> Penting:</strong> Pastikan semua data yang Anda masukkan sudah benar sebelum melakukan pembayaran, karena pembayaran yang sudah dikonfirmasi tidak dapat dibatalkan.
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Bagaimana cara mengajukan refund?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Untuk mengajukan refund:</p>
                            <ol>
                                <li>Hubungi customer service via WhatsApp</li>
                                <li>Kirimkan informasi berikut:
                                    <ul>
                                        <li>Bukti pembayaran (screenshot/receipt)</li>
                                        <li>Transaction ID</li>
                                        <li>Nomor WhatsApp yang digunakan saat pembelian</li>
                                        <li>Alasan refund</li>
                                    </ul>
                                </li>
                                <li>Tim kami akan verifikasi dalam 1-2 hari kerja</li>
                                <li>Jika disetujui, refund diproses dalam 3-7 hari kerja</li>
                            </ol>
                            <p><em>Refund akan dikurangi biaya admin payment gateway.</em></p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Voucher tidak diterima, bagaimana?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Jika voucher belum diterima setelah pembayaran:</p>
                            <ol>
                                <li><strong>Cek spam/folder lain</strong> di WhatsApp Anda</li>
                                <li><strong>Pastikan nomor WhatsApp aktif</strong> dan bisa menerima pesan</li>
                                <li><strong>Tunggu hingga 1 jam</strong> (untuk metode retail/VA)</li>
                                <li>Jika masih belum diterima, <strong>hubungi customer service</strong> dengan membawa:
                                    <ul>
                                        <li>Bukti pembayaran</li>
                                        <li>Transaction ID</li>
                                        <li>Nomor WhatsApp yang digunakan</li>
                                    </ul>
                                </li>
                            </ol>
                            <p>Tim kami akan segera membantu mengirim ulang voucher Anda.</p>
                        </div>
                    </div>
                </div>
                
                <!-- CATEGORY 4: TEKNIS & TROUBLESHOOTING -->
                <div class="faq-category">
                    <h2 class="category-title">
                        <i class="fa fa-wrench"></i> Teknis & Troubleshooting
                    </h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Koneksi lambat atau tidak stabil, kenapa?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Beberapa faktor yang mempengaruhi kecepatan:</p>
                            <ul>
                                <li><strong>Jarak dari WiFi:</strong> Semakin jauh, semakin lemah signal</li>
                                <li><strong>Jumlah pengguna:</strong> Banyak pengguna = bandwidth terbagi</li>
                                <li><strong>Cuaca:</strong> Hujan/badai dapat mempengaruhi koneksi</li>
                                <li><strong>Perangkat Anda:</strong> Device lawas mungkin tidak support kecepatan tinggi</li>
                                <li><strong>Maintenance ISP:</strong> Gangguan dari provider internet</li>
                            </ul>
                            
                            <p><strong>Solusi yang bisa dicoba:</strong></p>
                            <ol>
                                <li>Pindah lokasi lebih dekat ke access point</li>
                                <li>Restart WiFi dan perangkat Anda</li>
                                <li>Tutup aplikasi yang tidak digunakan</li>
                                <li>Hindari download/streaming berat jika paket terbatas</li>
                                <li>Gunakan di jam low traffic (dini hari)</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Halaman login tidak muncul otomatis, bagaimana?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Cara manual membuka halaman login:</p>
                            <ol>
                                <li><strong>Buka browser</strong> (Chrome/Firefox/Safari)</li>
                                <li><strong>Ketik alamat:</strong> <code>192.168.1.1</code> atau <code>login.wifi</code></li>
                                <li>Atau gunakan <strong>link login</strong> yang dikirim di WhatsApp</li>
                                <li>Jika tetap tidak muncul, coba:
                                    <ul>
                                        <li>Clear cache/cookies browser</li>
                                        <li>Gunakan mode incognito/private</li>
                                        <li>Coba browser lain</li>
                                        <li>Restart perangkat</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Apakah situs tertentu diblokir?</span>
                            <i class="fa fa-chevron-down icon"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Untuk keamanan dan kenyamanan bersama, beberapa situs mungkin diblokir:</p>
                            <ul>
                                <li>Situs pornografi dan konten dewasa</li>
                                <li>Situs judi online</li>
                                <li>Situs phishing dan malware</li>
                                <li>Situs yang melanggar hukum Indonesia</li>
                            </ul>
                            <p>Blocking dilakukan untuk:</p>
                            <ul>
                                <li>Kepatuhan terhadap regulasi pemerintah</li>
                                <li>Melindungi pengguna dari ancaman cyber</li>
                                <li>Menjaga bandwidth untuk konten legal</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- CONTACT CTA -->
                <div class="contact-cta">
                    <h3><i class="fa fa-comments"></i> Masih Ada Pertanyaan?</h3>
                    <p>Tim customer service kami siap membantu Anda!</p>
                    <?php if ($agent_phone): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $agent_phone); ?>" target="_blank">
                        <i class="fa fa-whatsapp"></i> Hubungi Kami di WhatsApp
                    </a>
                    <p style="margin-top: 15px; font-size: 0.9rem;">Jam operasional: 08:00 - 20:00 WIB</p>
                    <?php else: ?>
                    <p>Hubungi customer service untuk bantuan lebih lanjut</p>
                    <?php endif; ?>
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
    
    <script>
    $(document).ready(function() {
        // FAQ Toggle
        $('.faq-question').click(function() {
            $(this).toggleClass('active');
            $(this).next('.faq-answer').toggleClass('show');
        });
        
        // Search functionality
        $('#faqSearch').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            
            if (value === '') {
                $('.faq-item').show();
                $('.faq-category').show();
                $('.faq-answer').removeClass('show');
                $('.faq-question').removeClass('active');
            } else {
                $('.faq-item').each(function() {
                    var question = $(this).find('.faq-question span').text().toLowerCase();
                    var answer = $(this).find('.faq-answer').text().toLowerCase();
                    
                    if (question.indexOf(value) > -1 || answer.indexOf(value) > -1) {
                        $(this).show();
                        // Auto expand matched items
                        $(this).find('.faq-answer').addClass('show');
                        $(this).find('.faq-question').addClass('active');
                    } else {
                        $(this).hide();
                    }
                });
                
                // Hide categories with no visible items
                $('.faq-category').each(function() {
                    if ($(this).find('.faq-item:visible').length === 0) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
