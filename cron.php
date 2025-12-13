<?php

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Access denied');
}

require_once __DIR__ . '/vendor/autoload.php';

use ZEngine\Core\ZEngine;
use ZEngine\App\Tasks\DoSomething;

$app = ZEngine::init();

$cronKey = env('CRON_KEY');
$providedKey = $argv[1] ?? '';

if ($providedKey !== $cronKey) {
    die("Error: Invalid CRON_KEY\n");
}

echo "=== ZEngine Cron Jobs Started ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Cron-jobs goes here
    $doSomething = new DoSomething();
    $doSomething->handle();

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Cron Jobs Completed ===\n";
