<?php

return [
    'APP_NAME' => 'ZEngine',
    'APP_ENV' => 'local',
    'APP_KEY' => 'app_key',
    'APP_DEBUG' => true,
    'APP_URL' => 'http://localhost:1999',
    'APP_TIMEZONE' => 'UTC',
    'APP_LOCALE' => 'en',
    'CRON_KEY' => 'zengine_cron_key',
    'LOGGER_PATH' => __DIR__ . '/../storage/logs',
    'CACHE_PATH' => __DIR__ . '/../storage/cache',
    'CACHE_TTL' => 3600,

    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_PORT' => 3306,
    'DB_DATABASE' => 'zengine',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',

    'SESSION_NAME' => 'ZENGINE_SESSION',
    'SESSION_LIFETIME' => 7200,
    'SESSION_PATH' => '/',
    'SESSION_DOMAIN' => '',
    'SESSION_SECURE' => true,
    'SESSION_HTTPONLY' => true,
    'SESSION_SAMESITE' => 'Lax',
    'SESSION_STORAGE_PATH' => __DIR__ . '/../storage/sessions',

    'COOKIE_EXPIRE' => 3600,
    'COOKIE_PATH' => '/',
    'COOKIE_DOMAIN' => '',
    'COOKIE_SECURE' => true,
    'COOKIE_HTTPONLY' => true,
    'COOKIE_SAMESITE' => 'Lax',

    'SMTP_HOST' => '',
    'SMTP_PORT' => 587,
    'SMTP_USERNAME' => '',
    'SMTP_PASSWORD' => '',
    'SMTP_ENCRYPTION' => 'tls',
    'SMTP_FROM_ADDRESS' => '',
    'SMTP_FROM_NAME' => '',
    'SMTP_TIMEOUT' => 30,
    'SMTP_AUTH' => true,
    'SMTP_DEBUG' => 0,

    'HASH_ALGO' => 'bcrypt',
    'HASH_OPTIONS' => [
        'cost' => 12,
    ],

    'RATELIMIT_STORAGE_PATH' => __DIR__ . '/../storage/ratelimit',

    'MAINTENANCE_MODE' => 0,
    'MAINTENANCE_MESSAGE' => 'We are currently performing maintenance. Please check back soon.',
    'WHITELISTED_IPS' => ['127.0.0.1', '::1'],
];
