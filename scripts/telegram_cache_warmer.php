<?php
/**
 * Telegram Cache Warmer
 * Background script untuk pre-warming cache agar response selalu instant
 * 
 * Untuk Windows, jalankan via Task Scheduler setiap 5 menit:
 * Program: C:\xampp3\php\php.exe
 * Arguments: C:\xampp3\htdocs\mikhmon-fix\scripts\telegram_cache_warmer.php
 * 
 * Atau jalankan manual via browser:
 * http://localhost/mikhmon-fix/scripts/telegram_cache_warmer.php
 */

// Set working directory
chdir(__DIR__ . '/../');

// Load required files
require_once('include/config.php');
require_once('include/db_config.php');
require_once('lib/routeros_api.class.php');

/**
 * Pre-warm package cache
 */
function prewarmPackageCache() {
    global $data;
    
    $cacheFile = __DIR__ . '/../cache/telegram_packages.json';
    $cacheDir = dirname($cacheFile);
    
    // Create cache directory if not exists
    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    
    // Check if cache needs refresh (older than 8 minutes)
    if (file_exists($cacheFile)) {
        $cacheTime = filemtime($cacheFile);
        if ((time() - $cacheTime) < 480) { // 8 minutes
            echo "Cache still fresh, skipping refresh\n";
            return;
        }
    }
    
    echo "Pre-warming package cache...\n";
    
    try {
        // Get session config
        if (!isset($data) || empty($data)) {
            echo "No session config found\n";
            return;
        }
        
        // Get first session
        $sessions = array_keys($data);
        $session = null;
        foreach ($sessions as $s) {
            if ($s != 'mikhmon') {
                $session = $s;
                break;
            }
        }
        
        if (!$session) {
            echo "No MikroTik session found\n";
            return;
        }
        
        // Load session config
        $sessionData = $data[$session];
        $iphost = explode('!', $sessionData[1])[1] ?? '';
        $userhost = explode('@|@', $sessionData[2])[1] ?? '';
        $passwdhost = explode('#|#', $sessionData[3])[1] ?? '';
        $currency = explode('&', $sessionData[6])[1] ?? 'Rp';
        
        if (empty($iphost) || empty($userhost) || empty($passwdhost)) {
            echo "Incomplete session config\n";
            return;
        }
        
        // Connect to MikroTik
        $API = new RouterosAPI();
        $API->debug = false;
        $API->timeout = 10; // Longer timeout for background process
        
        if (!$API->connect($iphost, $userhost, decrypt($passwdhost))) {
            echo "Failed to connect to MikroTik\n";
            return;
        }
        
        // Get profiles
        $profiles = $API->comm("/ip/hotspot/user/profile/print");
        $API->disconnect();
        
        if (empty($profiles)) {
            echo "No profiles found\n";
            return;
        }
        
        // Process profiles
        $packages = [];
        foreach ($profiles as $profile) {
            $profileName = $profile['name'] ?? '';
            $ponlogin = $profile['on-login'] ?? '';
            
            if (empty($ponlogin) || $profileName === 'default') {
                continue;
            }
            
            $parts = explode(",", $ponlogin);
            $validity = $parts[3] ?? '';
            $sprice = $parts[4] ?? '0';
            
            if (empty($validity) || $sprice <= 0) {
                continue;
            }
            
            // Format price
            if (strpos($currency, 'Rp') !== false || strpos($currency, 'IDR') !== false) {
                $priceFormatted = $currency . " " . number_format((float)$sprice, 0, ",", ".");
            } else {
                $priceFormatted = $currency . " " . number_format((float)$sprice, 2);
            }
            
            $packages[] = [
                'name' => $profileName,
                'validity' => $validity,
                'price' => (float)$sprice,
                'price_formatted' => $priceFormatted,
                'display' => $profileName . " - " . $priceFormatted
            ];
        }
        
        // Sort by price (cheapest first)
        usort($packages, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        
        // Cache the result
        $cacheData = [
            'timestamp' => time(),
            'packages' => $packages,
            'prewarmed' => true
        ];
        
        file_put_contents($cacheFile, json_encode($cacheData));
        
        echo "Cache refreshed successfully. Found " . count($packages) . " packages\n";
        
        // Also create memory cache file for ultra-fast access
        $memoryCacheFile = $cacheDir . '/telegram_packages_memory.json';
        file_put_contents($memoryCacheFile, json_encode($packages));
        
    } catch (Exception $e) {
        echo "Error pre-warming cache: " . $e->getMessage() . "\n";
    }
}

/**
 * Clean old cache files
 */
function cleanOldCache() {
    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) {
        return;
    }
    
    $files = glob($cacheDir . '/telegram_*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $age = time() - filemtime($file);
            // Delete files older than 1 hour
            if ($age > 3600) {
                unlink($file);
                echo "Deleted old cache file: " . basename($file) . "\n";
            }
        }
    }
}

/**
 * Main execution
 */
function main() {
    echo "=== Telegram Cache Warmer Started ===\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n";
    
    // Pre-warm package cache
    prewarmPackageCache();
    
    // Clean old cache files
    cleanOldCache();
    
    echo "=== Cache Warmer Completed ===\n\n";
}

// Run if called directly
if (php_sapi_name() === 'cli') {
    main();
} else {
    // Web interface for manual trigger
    header('Content-Type: text/plain');
    main();
}
?>
