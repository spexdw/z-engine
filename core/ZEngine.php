<?php

namespace ZEngine\Core;

use ZEngine\Core\Container;
use ZEngine\Core\Router\Router;
use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;
use ZEngine\Core\ErrorHandler;
use ZEngine\Core\Providers;

class ZEngine
{
    private static ?self $instance = null;
    private Container $container;
    private array $services = [];
    private bool $booted = false;
    private array $config = [];

    private function __construct()
    {
        $this->container = new Container();
        $this->container->singleton(self::class, $this);
        $this->container->singleton('app', $this);
        $this->registerErrorHandler();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function init(array $config = []): self
    {
        $app = self::getInstance();
        $app->config = $config;
        $app->registerCoreServices();
        $app->boot();
        return $app;
    }

    private function registerErrorHandler(): void
    {
        $errorHandler = new ErrorHandler($this);
        $errorHandler->register();
    }

    private function registerCoreServices(): void
    {
        $this->container->singleton(Request::class, fn() => Request::capture());
        $this->container->singleton(Response::class, fn() => new Response());
        $this->container->singleton(Router::class, fn() => new Router($this->container));

        $this->registerService('request', Request::class);
        $this->registerService('response', Response::class);
        $this->registerService('router', Router::class);

        Providers::register($this->container);
    }

    public function registerService(string $name, string|object $service): void
    {
        if (is_string($service)) {
            $this->container->singleton($name, fn() => $this->container->make($service));
        } else {
            $this->container->singleton($name, $service);
        }
        $this->services[$name] = $service;
    }

    private function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->setTimezone();
        $this->setLocale();

        $this->booted = true;
    }

    private function setTimezone(): void
    {
        $timezone = env('APP_TIMEZONE', 'UTC');
        date_default_timezone_set($timezone);
    }

    private function setLocale(): void
    {
        $locale = env('APP_LOCALE', 'en');
        setlocale(LC_ALL, $locale);
    }

    public function get(string $name): mixed
    {
        return $this->container->make($name);
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function run(): void
    {
        $request = $this->get('request');
        $router = $this->get('router');

        $response = $router->dispatch($request);
        $response->send();
    }
}
