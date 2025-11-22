<?php

return [
    'expire' => env('COOKIE_EXPIRE', 3600),
    'path' => env('COOKIE_PATH', '/'),
    'domain' => env('COOKIE_DOMAIN', ''),
    'secure' => env('COOKIE_SECURE', false),
    'httponly' => env('COOKIE_HTTPONLY', true),
    'samesite' => env('COOKIE_SAMESITE', 'Lax'),
];
