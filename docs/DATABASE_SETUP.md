# Panduan Instalasi Database MikhMon Agent

Panduan ini memastikan instalasi baru memiliki seluruh tabel, kolom, dan data awal
termasuk fitur Digiflazz. Jalankan langkah secara berurutan.

## 1. Persiapan

1. Buat database MySQL/MariaDB kosong (contoh nama: `mikhmon_agents`).
2. Perbarui kredensial pada `include/db_config.php` sesuai server baru.
3. Pastikan akun DB memiliki izin `CREATE`, `ALTER`, dan `INSERT`.

## 2. Jalankan Skrip Perbaikan Terpadu (Direkomendasikan)

1. Upload `fix_all_modules.php` ke server.
2. Akses sekali via browser/CLI dengan `?key=fix-all-2024`, misalnya `https://domain/fix_all_modules.php?key=fix-all-2024`.
3. Pastikan log menampilkan pesan sukses dan ringkasan jumlah baris untuk tabel seperti `agents`, `billing_settings`, `billing_portal_otps`, `payment_methods`, dan `digiflazz_transactions`.
4. Hapus file tersebut setelah berhasil dijalankan demi keamanan.

Skrip ini memastikan semua struktur inti tersedia:

- Modul Agent & Public Sales (`agents`, `agent_settings`, `agent_prices`, `agent_profile_pricing`, `public_sales`, `payment_methods`, `site_pages`, `voucher_settings`).
- Modul Billing & OTP portal (`billing_profiles`, `billing_customers`, `billing_invoices`, `billing_payments`, `billing_logs`, `billing_settings`, `billing_portal_otps`, `agent_billing_payments`).
- Integrasi Digiflazz (`digiflazz_transactions`) dan seed data default (Tripay, paket portal, pengaturan OTP).

Referensi URL penting (sesuaikan domain instalasi):

- Portal pelanggan (login OTP): `https://domain/public/billing_login.php`
- Katalog voucher publik: `https://domain/public/index.php?agent=AG001`
- Panel agent: `https://domain/agent/index.php`

## 3. Alternatif: Installer PHP

- `install_database_ultimate.php`
  - Cocok untuk instalasi baru dari nol (termasuk seed data penuh dan verifikasi interaktif).
  - Setelah instalasi, tetap jalankan `fix_all_modules.php` untuk memastikan konfigurasi terbaru.
- `install_database.php`
  - Versi ringan untuk memastikan tabel agent & payment dasar saja.

Installer aman dijalankan ulang bila diperlukan, tetapi `fix_all_modules.php` adalah satu-satunya skrip yang perlu dibawa ke deployment baru untuk perbaikan struktur.

## 4. Cron Otomatisasi Billing (Opsional)

Tambahkan cron job bila ingin otomatisasi invoice/reminder:

```bash
# Generate invoice bulanan, reminder WA, isolasi/revert profil MikroTik
* * * * * php /path/to/project/process/billing_cron.php
```

Atur frekuensi sesuai kebutuhan (disarankan minimal sekali sehari).

## 5. Konfigurasi Digiflazz

Setelah tabel siap, perbarui nilai `agent_settings` berikut:

| Setting key                      | Keterangan                     |
|----------------------------------|--------------------------------|
| `digiflazz_enabled`              | Set `1` untuk mengaktifkan      |
| `digiflazz_username`             | Username Digiflazz              |
| `digiflazz_api_key`              | API key Digiflazz               |
| `digiflazz_allow_test`           | `1` bila ingin mode testing     |
| `digiflazz_default_markup_nominal` | Markup default (rupiah)      |
| `digiflazz_last_sync`            | Diisi otomatis saat sync harga  |

> Gunakan SQL `UPDATE agent_settings SET setting_value = '...' WHERE setting_key = '...'`.

## 6. Konfigurasi Billing Payments Agen

Agen dapat membayar tagihan pelanggan langsung dari saldo mereka. Setelah tabel siap, perbarui `agent_settings` untuk agen tertentu:

| Setting key                      | Keterangan                     |
|----------------------------------|--------------------------------|
| `billing_payment_fee_enabled`    | Set `1` untuk mengaktifkan fee   |
| `billing_payment_fee_percent`    | Fee dalam persen (mis. `2.5`)    |
| `billing_payment_broadcast_template` | Template pesan broadcast ke pelanggan |

> Agen dapat mengakses fitur ini via `https://domain/agent/billing_payments.php` setelah login.

## 7. Verifikasi

Setelah seluruh skrip dijalankan:

1. Cek keberadaan tabel penting:
   ```sql
   SHOW TABLES LIKE 'billing_portal_otps';
   SHOW TABLES LIKE 'agent_billing_payments';
   SHOW TABLES LIKE 'digiflazz_transactions';
   SHOW TABLES LIKE 'payment_methods';
   ```

2. Periksa isi dasar:
   ```sql
   SELECT COUNT(*) FROM agents;
   SELECT setting_key, setting_value FROM agent_settings WHERE setting_key LIKE 'digiflazz_%';
   SELECT COUNT(*) FROM payment_methods;
   SELECT setting_key, setting_value FROM billing_settings WHERE setting_key LIKE 'billing_portal_%';
   ```

3. Untuk memastikan tidak ada kolom hilang, jalankan `DESCRIBE <nama_tabel>`.

## 7. Troubleshooting

- **Error hak akses**: Pastikan user DB memiliki izin `CREATE/ALTER`.
- **Kolom hilang setelah restore lama**: Jalankan `fix_all_modules.php` terbaru. Skrip ini idempotent dan aman diulang.
- **Integrasi Digiflazz tidak aktif**: Pastikan `digiflazz_enabled = 1` dan kredensial benar.
- **Curl/OpenSSL**: Server harus memiliki ekstensi PHP cURL dan OpenSSL aktif untuk call API Digiflazz.

## 8. Catatan Migrasi

- Bila memulihkan dari backup lama, jalankan skrip di atas setelah restore untuk menambah kolom/tabel baru.
- Skrip `install_database_ultimate.php` bisa dijalankan kapan saja untuk memastikan struktur tetap up-to-date tanpa menghapus data.

---
Dokumen ini berada di `docs/DATABASE_SETUP.md`. Perbarui bila ada perubahan struktur baru.
