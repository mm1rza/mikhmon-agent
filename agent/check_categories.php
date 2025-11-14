<?php
include_once('../include/db_config.php');

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT DISTINCT category FROM digiflazz_products WHERE category IS NOT NULL AND category != '' ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Available categories in database:\n";
    foreach ($categories as $cat) {
        echo "- " . $cat . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>