<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('./include/db_config.php');
require_once(__DIR__ . '/../lib/BillingService.class.php');

echo "<pre>";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Loaded Extensions:\n";
print_r(get_loaded_extensions());

try {
    $db = getDBConnection();
    echo "\nDB Connection: OK\n";
    $stmt = $db->query("SHOW TABLES LIKE 'billing_invoices'");
    echo "billing_invoices table exists: " . ($stmt->rowCount() > 0 ? 'YES' : 'NO') . "\n";
} catch (Throwable $e) {
    echo "\nDB Connection FAILED: " . $e->getMessage() . "\n";
}

try {
    $billingService = new BillingService();
    echo "\nBillingService instantiated successfully.\n";
    $summary = $billingService->getDashboardSummary(date('Y-m'));
    echo "Summary sample:\n";
    print_r($summary);
} catch (Throwable $e) {
    echo "\nBillingService error: " . $e->getMessage() . "\n";
}

echo "\n__DIR__: " . __DIR__ . "\n";
echo "Current working dir: " . getcwd() . "\n";

echo "</pre>";
