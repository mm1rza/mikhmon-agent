@echo off
REM Setup Windows Task Scheduler untuk Telegram Cache Warmer
REM Jalankan sebagai Administrator

echo Setting up Telegram Cache Warmer Task...

REM Buat task yang berjalan setiap 5 menit
schtasks /create /tn "TelegramCacheWarmer" /tr "C:\xampp3\php\php.exe C:\xampp3\htdocs\mikhmon-fix\scripts\telegram_cache_warmer.php" /sc minute /mo 5 /f

if %errorlevel% equ 0 (
    echo ✅ Task berhasil dibuat!
    echo Task akan berjalan setiap 5 menit untuk pre-warming cache
    echo.
    echo Untuk melihat task:
    echo schtasks /query /tn "TelegramCacheWarmer"
    echo.
    echo Untuk menghapus task:
    echo schtasks /delete /tn "TelegramCacheWarmer" /f
) else (
    echo ❌ Gagal membuat task. Pastikan menjalankan sebagai Administrator
)

pause
