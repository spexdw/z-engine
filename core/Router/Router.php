<?php

namespace ZEngine\Core\Router;

use ZEngine\Core\Container;
use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;
use Closure;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private Container $container;
    private array $currentMiddleware = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function any(string $uri, Closure|array|string $action): Route
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        $route = null;
        foreach ($methods as $method) {
            $route = $this->addRoute($method, $uri, $action);
        }
        return $route;
    }

    private function addRoute(string $method, string $uri, Closure|array|string $action): Route
    {
        $fullUri = $this->currentPrefix . $uri;
        $route = new Route($method, $fullUri, $action);
        $this->routes[$method][] = $route;
        return $route;
    }

    public function group(array $attributes, Closure $callback): void
    {
        $prefix = $attributes['prefix'] ?? '';
        $middleware = $attributes['middleware'] ?? [];

        $previousPrefix = $this->getCurrentPrefix();
        $previousMiddleware = $this->currentMiddleware ?? [];

        $this->setPrefix($previousPrefix . $prefix);
        $this->currentMiddleware = array_merge($previousMiddleware, (array) $middleware);

        $beforeCounts = [];
        foreach ($this->routes as $method => $routes) {
            $beforeCounts[$method] = count($routes);
        }

        $callback($this);

        foreach ($this->routes as $method => $routes) {
            $startIndex = $beforeCounts[$method] ?? 0;
            for ($i = $startIndex; $i < count($routes); $i++) {
                if (!empty($this->currentMiddleware)) {
                    $this->routes[$method][$i]->middleware($this->currentMiddleware);
                }
            }
        }

        $this->setPrefix($previousPrefix);
        $this->currentMiddleware = $previousMiddleware;
    }

    private string $currentPrefix = '';
    private array $lastRouteCounts = [];

    private function getCurrentPrefix(): string
    {
        return $this->currentPrefix;
    }

    private function setPrefix(string $prefix): void
    {
        $this->lastRouteCounts = [];
        foreach ($this->routes as $method => $methodRoutes) {
            $this->lastRouteCounts[$method] = count($methodRoutes);
        }
        $this->currentPrefix = $prefix;
    }

    private function getLastGroupRoutes(): array
    {
        $routes = [];
        foreach ($this->routes as $method => $methodRoutes) {
            $startIndex = $this->lastRouteCounts[$method] ?? 0;
            $newRoutes = array_slice($methodRoutes, $startIndex);
            $routes = array_merge($routes, $newRoutes);
        }
        return $routes;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = $request->path();

        if (!isset($this->routes[$method])) {
            return Response::view('errors/500', [], 405);
        }

        foreach ($this->routes[$method] as $route) {
            $params = $route->matches($uri);
            if ($params !== false) {
                return $this->runRoute($route, $params, $request);
            }
        }

        return Response::view('errors/404', [], 404);
    }

    private function runRoute(Route $route, array $params, Request $request): Response
    {
        $request->setParams($params);

        $middlewares = $route->getMiddleware();

        $pipeline = array_reduce(
            array_reverse($middlewares),
            fn($next, $middleware) => fn($req) => $this->runMiddleware($middleware, $req, $next),
            fn($req) => $this->callAction($route, $params, $req)
        );

        $result = $pipeline($request);

        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }

        return Response::make($result);
    }

    private function runMiddleware(string $middleware, Request $request, Closure $next): mixed
    {
        $instance = $this->container->make($middleware);
        return $instance->handle($request, $next);
    }

    private function callAction(Route $route, array $params, Request $request): mixed
    {
        $action = $route->getAction();

        if ($action instanceof Closure) {
            return $this->container->call($action, array_merge($params, ['request' => $request]));
        }

        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
            $instance = $this->container->make($controller);
            return $this->container->call([$instance, $method], array_merge($params, ['request' => $request]));
        }

        if (is_array($action)) {
            [$controller, $method] = $action;
            $instance = is_string($controller) ? $this->container->make($controller) : $controller;
            return $this->container->call([$instance, $method], array_merge($params, ['request' => $request]));
        }

        throw new \Exception("Invalid route action");
    }
}
