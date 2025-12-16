<?php

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        static $config = null;

        if ($config === null) {
            $envFile = dirname(__DIR__) . '/config/env.php';
            $config = file_exists($envFile) ? require $envFile : [];
        }

        return $config[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $config = [];

        if (empty($config)) {
            $configPath = dirname(__DIR__) . '/config/';
            foreach (glob($configPath . '*.php') as $file) {
                $name = basename($file, '.php');
                $config[$name] = require $file;
            }
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        $app = \ZEngine\Core\ZEngine::getInstance();

        if ($abstract === null) {
            return $app;
        }

        return $app->get($abstract);
    }
}

if (!function_exists('request')) {
    function request(): \ZEngine\Core\Http\Request
    {
        return app('request');
    }
}

if (!function_exists('response')) {
    function response(): \ZEngine\Core\Http\Response
    {
        return app('response');
    }
}

if (!function_exists('router')) {
    function router(): \ZEngine\Core\Router\Router
    {
        return app('router');
    }
}

if (!function_exists('db')) {
    function db(): \ZEngine\Core\Services\DatabaseService
    {
        return app('db');
    }
}

if (!function_exists('session')) {
    function session(): \ZEngine\Core\Services\SessionService
    {
        return app('session');
    }
}

if (!function_exists('cookie')) {
    function cookie(): \ZEngine\Core\Services\CookieService
    {
        return app('cookie');
    }
}

if (!function_exists('form')) {
    function form(): \ZEngine\Core\Services\FormBuilderService
    {
        return app('form');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $statusCode = 302): \ZEngine\Core\Http\Response
    {
        return \ZEngine\Core\Http\Response::redirect($url, $statusCode);
    }
}

if (!function_exists('json')) {
    function json(mixed $data, int $statusCode = 200): \ZEngine\Core\Http\Response
    {
        return \ZEngine\Core\Http\Response::json($data, $statusCode);
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = []): \ZEngine\Core\Http\Response
    {
        return \ZEngine\Core\Http\Response::view($view, $data);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    function dump(...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
    }
}

if (!function_exists('cache')) {
    function cache(): \ZEngine\Core\Services\CacheService
    {
        return app('cache');
    }
}

if (!function_exists('validator')) {
    function validator(): \ZEngine\Core\Services\ValidationService
    {
        return app('validator');
    }
}

if (!function_exists('logger')) {
    function logger(): \ZEngine\Core\Services\LoggerService
    {
        return app('logger');
    }
}

if (!function_exists('hash')) {
    function hash_service(): \ZEngine\Core\Services\HashService
    {
        return app('hash');
    }
}

if (!function_exists('mail')) {
    function mail(): \ZEngine\Core\Services\MailService
    {
        return app('mail');
    }
}

if (!function_exists('event')) {
    function event(): \ZEngine\Core\Services\EventService
    {
        return app('event');
    }
}
