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
    }

    private static function registerDatabase(Container $container): void
    {
        $container->singleton('db', function () {
            $config = config('database.connections.' . config('database.default'));
            return new DatabaseService($config);
        });
        $container->alias('db', DatabaseService::class);
    }

    private static function registerSession(Container $container): void
    {
        $container->singleton('session', function () {
            return SessionService::getInstance();
        });
        $container->alias('session', SessionService::class);
    }

    private static function registerCookie(Container $container): void
    {
        $container->singleton('cookie', function () {
            return new CookieService();
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
            $config = config('cache', []);
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
            return new LoggerService();
        });
        $container->alias('logger', LoggerService::class);
    }

    private static function registerHash(Container $container): void
    {
        $container->singleton('hash', function () {
            return new HashService();
        });
        $container->alias('hash', HashService::class);
    }

    private static function registerMail(Container $container): void
    {
        $container->singleton('mail', function () {
            return new MailService(config('mail'));
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

}
