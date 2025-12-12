<?php
/*
 * SOLUTION 4: Cron Job for Telegram Callback Processing
 * Run this every minute: * * * * * php /path/to/telegram_callback_cron.php
 */

// Load the processor
require_once(__DIR__ . '/../api/telegram_callback_processor.php');

// Process the queue
processCallbackQueue();

echo "Telegram callback queue processed at " . date('Y-m-d H:i:s') . "\n";
?>
