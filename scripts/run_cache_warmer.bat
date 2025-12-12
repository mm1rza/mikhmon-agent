@echo off
REM Manual Cache Warmer Runner untuk Windows
echo Running Telegram Cache Warmer...

REM Cari PHP executable
set PHP_PATH=""
if exist "C:\xampp3\php\php.exe" set PHP_PATH="C:\xampp3\php\php.exe"
if exist "C:\xampp\php\php.exe" set PHP_PATH="C:\xampp\php\php.exe"
if exist "C:\wamp64\bin\php\php8.2.12\php.exe" set PHP_PATH="C:\wamp64\bin\php\php8.2.12\php.exe"

if %PHP_PATH%=="" (
    echo ❌ PHP tidak ditemukan. Pastikan XAMPP/WAMP terinstall.
    echo Atau edit script ini untuk menunjuk ke lokasi php.exe yang benar.
    pause
    exit
)

echo Using PHP: %PHP_PATH%
echo.

REM Jalankan cache warmer
%PHP_PATH% "%~dp0telegram_cache_warmer.php"

echo.
echo Cache warmer selesai dijalankan.
pause
