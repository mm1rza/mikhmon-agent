<?php
/**
 * Fix WhatsApp Settings for Public Voucher
 * Run this file once to add WhatsApp API settings
 */

// Security key
$securityKey = $_GET['key'] ?? '';
if ($securityKey !== 'fix-wa-2024') {
    exit("Access denied. Tambahkan ?key=fix-wa-2024 pada URL\n");
}

require_once __DIR__ . '/include/db_config.php';

try {
    $pdo = getDBConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Fix WhatsApp Settings</h2>";
    echo "<hr>";
    
    // Check current settings
    echo "<h3>1. Checking current WhatsApp settings...</h3>";
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM agent_settings WHERE setting_key LIKE 'whatsapp_%'");
    $current = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($current)) {
        echo "<p style='color: red;'>❌ No WhatsApp settings found!</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Setting Key</th><th>Setting Value</th></tr>";
        foreach ($current as $row) {
            $value = empty($row['setting_value']) ? '<span style="color:red;">EMPTY!</span>' : htmlspecialchars($row['setting_value']);
            echo "<tr><td>{$row['setting_key']}</td><td>{$value}</td></tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h3>2. Adding/Updating WhatsApp settings...</h3>";
    
    // IMPORTANT: Ganti dengan API URL dan API Key Anda yang sebenarnya!
    $whatsapp_settings = [
        'whatsapp_api_url' => 'https://api.fonnte.com/send',  // Ganti dengan URL gateway Anda
        'whatsapp_api_key' => 'YOUR_API_KEY_HERE',            // Ganti dengan API Key Anda
    ];
    
    // Get first agent ID
    $stmt = $pdo->query("SELECT id FROM agents ORDER BY id LIMIT 1");
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);
    $agentId = $agent['id'] ?? 1;
    
    $stmt = $pdo->prepare("INSERT INTO agent_settings (agent_id, setting_key, setting_value, setting_type, description) 
                          VALUES (:agent_id, :key, :value, 'string', :description)
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    foreach ($whatsapp_settings as $key => $value) {
        $description = $key === 'whatsapp_api_url' ? 'WhatsApp API Gateway URL' : 'WhatsApp API Key/Token';
        
        $stmt->execute([
            ':agent_id' => $agentId,
            ':key' => $key,
            ':value' => $value,
            ':description' => $description
        ]);
        
        echo "<p>✅ Setting <strong>$key</strong> = <code>$value</code></p>";
    }
    
    echo "<hr>";
    echo "<h3>3. Verifying settings...</h3>";
    
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM agent_settings WHERE setting_key LIKE 'whatsapp_%'");
    $updated = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Setting Key</th><th>Setting Value</th><th>Status</th></tr>";
    foreach ($updated as $row) {
        $isEmpty = empty($row['setting_value']);
        $status = $isEmpty ? '<span style="color:red;">❌ EMPTY</span>' : '<span style="color:green;">✅ OK</span>';
        $value = $isEmpty ? '<span style="color:red;">EMPTY!</span>' : htmlspecialchars($row['setting_value']);
        echo "<tr><td>{$row['setting_key']}</td><td>{$value}</td><td>{$status}</td></tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>⚠️ IMPORTANT!</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
    echo "<p><strong>Jika Anda melihat 'YOUR_API_KEY_HERE', Anda HARUS:</strong></p>";
    echo "<ol>";
    echo "<li>Edit file <code>fix_whatsapp_settings.php</code></li>";
    echo "<li>Ganti <code>whatsapp_api_url</code> dengan URL gateway WhatsApp Anda (contoh: https://api.fonnte.com/send)</li>";
    echo "<li>Ganti <code>YOUR_API_KEY_HERE</code> dengan API Key yang sebenarnya</li>";
    echo "<li>Jalankan file ini lagi</li>";
    echo "</ol>";
    echo "<p><strong>Atau update manual via SQL:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>";
    echo "UPDATE agent_settings SET setting_value = 'https://api.fonnte.com/send' WHERE setting_key = 'whatsapp_api_url';\n";
    echo "UPDATE agent_settings SET setting_value = 'YOUR_ACTUAL_API_KEY' WHERE setting_key = 'whatsapp_api_key';";
    echo "</pre>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>✅ Done!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Pastikan API URL dan API Key sudah benar</li>";
    echo "<li>Test dengan transaksi baru</li>";
    echo "<li>Cek log: <code>VoucherGenerator: Sending WhatsApp to...</code></li>";
    echo "<li><strong>HAPUS file ini setelah selesai!</strong></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
