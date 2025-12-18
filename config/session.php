<?php

return [
    'name' => env('SESSION_NAME', 'ZENGINE_SESSION'),
    'lifetime' => env('SESSION_LIFETIME', 7200),
    'path' => env('SESSION_PATH', '/'),
    'domain' => env('SESSION_DOMAIN', ''),
    'secure' => env('SESSION_SECURE', false),
    'httponly' => env('SESSION_HTTPONLY', true),
    'samesite' => env('SESSION_SAMESITE', 'Lax'),
    'storage_path' => env('SESSION_STORAGE_PATH', __DIR__ . '/../storage/sessions'),
];
