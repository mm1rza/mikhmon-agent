<?php
/**
 * CLI utility to synchronize Digiflazz price list.
 * Usage (from project root):
 *   php process/sync_digiflazz.php
 */

require_once __DIR__ . '/../include/db_config.php';
require_once __DIR__ . '/../lib/DigiflazzClient.class.php';

default_timezone_check();

try {
    $client = new DigiflazzClient();

    if (!$client->isEnabled()) {
        throw new RuntimeException('Digiflazz integration is not enabled. Configure credentials first.');
    }

    echo "[" . date('Y-m-d H:i:s') . "] Starting Digiflazz price list sync..." . PHP_EOL;

    $result = $client->syncPriceList();
    $report = $result['report'] ?? ['prepaid' => 0, 'postpaid' => 0];

    echo sprintf(
        "[%s] Sync completed. Prepaid: %d, Postpaid: %d" . PHP_EOL,
        date('Y-m-d H:i:s'),
        (int)$report['prepaid'],
        (int)$report['postpaid']
    );

    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, '[' . date('Y-m-d H:i:s') . "] Sync failed: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

function default_timezone_check(): void
{
    if (!ini_get('date.timezone')) {
        date_default_timezone_set('Asia/Jakarta');
    }
}
