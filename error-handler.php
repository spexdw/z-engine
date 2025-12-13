<?php

$allowedCodes = ['403', '404', '500'];
$code = $_GET['code'] ?? '404';

if (!in_array($code, $allowedCodes, true)) {
    $code = '404';
}

http_response_code((int)$code);

require __DIR__ . '/app/Views/errors/' . $code . '.php';
exit;
