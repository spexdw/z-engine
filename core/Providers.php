<?php

namespace ZEngine\Core;

use ZEngine\Core\Services\DatabaseService;
use ZEngine\Core\Services\SessionService;
use ZEngine\Core\Services\CookieService;
use ZEngine\Core\Services\FormBuilderService;
use ZEngine\Core\Services\CacheService;
use ZEngine\Core\Services\ValidationService;
use ZEngine\Core\Services\LoggerService;
use ZEngine\Core\Services\HashService;
use ZEngine\Core\Services\MailService;
use ZEngine\Core\Services\EventService;
use ZEngine\Core\Services\RateLimitService;

class Providers
{
    public static function register(Container $container): void
    {
        self::registerDatabase($container);
        self::registerSession($container);
        self::registerCookie($container);
        self::registerFormBuilder($container);
        self::registerCache($container);
        self::registerValidation($container);
        self::registerLogger($container);
        self::registerHash($container);
        self::registerMail($container);
        self::registerEvent($container);
        self::registerRateLimit($container);
    }

    private static function registerDatabase(Container $container): void
    {
        $container->singleton('db', function () {
            $config = [
                'driver' => env('DB_CONNECTION', 'mysql'),
                'host' => env('DB_HOST', 'localhost'),
                'port' => env('DB_PORT', 3306),
                'database' => env('DB_DATABASE', ''),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4'
            ];
            return new DatabaseService($config);
        });
        $container->alias('db', DatabaseService::class);
    }

    private static function registerSession(Container $container): void
    {
        $container->bind('session', function () {
            return SessionService::getInstance();
        });
        $container->alias('session', SessionService::class);
    }

    private static function registerCookie(Container $container): void
    {
        $container->singleton('cookie', function () {
            $config = [
                'expire' => env('COOKIE_EXPIRE', 3600),
                'path' => env('COOKIE_PATH', '/'),
                'domain' => env('COOKIE_DOMAIN', ''),
                'secure' => env('COOKIE_SECURE', false),
                'httponly' => env('COOKIE_HTTPONLY', true),
                'samesite' => env('COOKIE_SAMESITE', 'Lax')
            ];
            return new CookieService($config);
        });
        $container->alias('cookie', CookieService::class);
    }

    private static function registerFormBuilder(Container $container): void
    {
        $container->singleton('form', function () {
            return new FormBuilderService();
        });
        $container->alias('form', FormBuilderService::class);
    }

    private static function registerCache(Container $container): void
    {
        $container->singleton('cache', function () {
            $config = [
                'path' => env('CACHE_PATH'),
                'ttl' => env('CACHE_TTL', 3600)
            ];
            return new CacheService($config);
        });
        $container->alias('cache', CacheService::class);
    }

    private static function registerValidation(Container $container): void
    {
        $container->singleton('validator', function () {
            return new ValidationService();
        });
        $container->alias('validator', ValidationService::class);
    }

    private static function registerLogger(Container $container): void
    {
        $container->singleton('logger', function () {
            $config = [
                'path' => env('LOGGER_PATH')
            ];
            return new LoggerService($config);
        });
        $container->alias('logger', LoggerService::class);
    }

    private static function registerHash(Container $container): void
    {
        $container->singleton('hash', function () {
            $algoMap = [
                'bcrypt' => PASSWORD_BCRYPT,
                'argon2i' => PASSWORD_ARGON2I,
                'argon2id' => PASSWORD_ARGON2ID,
                'default' => PASSWORD_DEFAULT
            ];

            $algoName = strtolower(env('HASH_ALGO', 'bcrypt'));
            $algo = $algoMap[$algoName] ?? PASSWORD_BCRYPT;

            $config = [
                'algo' => $algo,
                'options' => env('HASH_OPTIONS', ['cost' => 12])
            ];
            return new HashService($config);
        });
        $container->alias('hash', HashService::class);
    }

    private static function registerMail(Container $container): void
    {
        $container->singleton('mail', function () {
            $config = [
                'SMTP_HOST' => env('SMTP_HOST'),
                'SMTP_PORT' => env('SMTP_PORT', 587),
                'SMTP_USERNAME' => env('SMTP_USERNAME'),
                'SMTP_PASSWORD' => env('SMTP_PASSWORD'),
                'SMTP_ENCRYPTION' => env('SMTP_ENCRYPTION', 'tls'),
                'SMTP_FROM_ADDRESS' => env('SMTP_FROM_ADDRESS'),
                'SMTP_FROM_NAME' => env('SMTP_FROM_NAME'),
                'SMTP_TIMEOUT' => env('SMTP_TIMEOUT', 30),
                'SMTP_AUTH' => env('SMTP_AUTH', true),
                'SMTP_DEBUG' => env('SMTP_DEBUG', 0)
            ];
            return new MailService($config);
        });
        $container->alias('mail', MailService::class);
    }

    private static function registerEvent(Container $container): void
    {
        $container->singleton('event', function () {
            return new EventService();
        });
        $container->alias('event', EventService::class);
    }

    private static function registerRateLimit(Container $container): void
    {
        $container->bind('ratelimit', function () {
            return RateLimitService::getInstance();
        });
        $container->alias('ratelimit', RateLimitService::class);
    }

}
