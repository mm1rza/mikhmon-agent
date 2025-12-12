# Script Profile ISOLIR - Auto Delete Scheduler

## Deskripsi
Script ini digunakan pada profile ISOLIR untuk menghapus scheduler auto-expiry yang dibuat sebelumnya saat user login menggunakan profile isolir.

## Cara Kerja
1. Saat user login menggunakan profile ISOLIR, script ini akan dijalankan
2. Script akan mencari scheduler dengan nama "exp-namauser"
3. Jika scheduler ditemukan, akan dihapus otomatis
4. Log aktivitas akan dicatat di system log

## Cara Penggunaan

### 1. Buat Profile ISOLIR
- Buka **PPP → Profiles → Add**
- Buat profile baru dengan nama "ISOLIR" (atau nama lain)
- Set rate limit yang sangat rendah (misal: 64k/64k)
- **JANGAN** centang "Enable Auto Isolir" pada profile ini

### 2. Tambahkan Script
Copy script berikut ke field **Script (On-Up)**:

```
:local pengguna $"user"; :local date [/system clock get date]; :local time [/system clock get time]; :log warning "User $pengguna login menggunakan profile ISOLIR pada $time tanggal $date"; :local schedulerName "$pengguna"; :local schedulerID [/system scheduler find name=$schedulerName]; :if ($schedulerID != "") do={ /system scheduler remove $schedulerID; :log info "Scheduler '$schedulerName' berhasil dihapus karena user $pengguna menggunakan profile isolir"; } else={ :log info "Tidak ada scheduler '$schedulerName' yang perlu dihapus untuk user $pengguna"; }
```

### 3. Gunakan Profile ISOLIR
- Profile ini akan digunakan oleh sistem Auto Isolir
- Saat user di-isolir, mereka akan dipindah ke profile ini
- Script akan otomatis menghapus scheduler yang ada

## Alur Kerja Lengkap

1. **User Normal Login** → Profile normal dengan Auto Isolir → Scheduler dibuat
2. **Waktu Habis** → User dipindah ke Profile ISOLIR → Scheduler dihapus otomatis
3. **User Login Lagi** → Tetap di Profile ISOLIR → Tidak ada scheduler baru

## Log yang Dihasilkan

### Saat User Login dengan Profile ISOLIR:
```
warning User john_doe login menggunakan profile ISOLIR pada 14:30:25 tanggal dec/09/2025
info Scheduler 'john_doe' berhasil dihapus karena user john_doe menggunakan profile isolir
info Comment user john_doe diupdate: scheduler dihapus
```

### Jika Tidak Ada Scheduler:
```
warning User john_doe login menggunakan profile ISOLIR pada 14:30:25 tanggal dec/09/2025
info Tidak ada scheduler 'john_doe' yang perlu dihapus untuk user john_doe
info Comment user john_doe diupdate: login isolir tanpa scheduler
```

## Comment User yang Dihasilkan

### Saat Scheduler Auto Isolir Dibuat:
```
Comment: AUTO-ISOLIR: Scheduler dibuat pada dec/09/2025 14:30:25, expire 30d
```

### Saat Scheduler Dihapus (Login dengan Profile ISOLIR):
```
Comment: ISOLIR: Scheduler dihapus pada dec/09/2025 15:45:10
```

### Saat Login ISOLIR Tanpa Scheduler:
```
Comment: ISOLIR: Login dengan profile isolir pada dec/09/2025 15:45:10
```

## Keuntungan
- ✅ Mencegah konflik scheduler
- ✅ Membersihkan scheduler yang tidak terpakai
- ✅ Log aktivitas yang jelas
- ✅ Otomatis tanpa intervensi manual
- ✅ **Tracking via comment user** - Status auto-isolir tercatat di comment user
- ✅ **Monitoring mudah** - Admin bisa lihat status user dari comment

## Catatan Penting
- Script ini HANYA untuk profile ISOLIR
- JANGAN gunakan di profile normal yang memiliki Auto Isolir
- Pastikan nama scheduler sesuai format "username" (tanpa prefix)
