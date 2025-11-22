<?php

namespace ZEngine\Core\Router;

use Closure;

class Route
{
    private string $method;
    private string $uri;
    private Closure|array|string $action;
    private array $middleware = [];
    private array $parameters = [];

    public function __construct(string $method, string $uri, Closure|array|string $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
    }

    public function middleware(string|array $middleware): self
    {
        $this->middleware = array_merge($this->middleware, (array) $middleware);
        return $this;
    }

    public function matches(string $uri): array|false
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $this->uri);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);

            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $this->uri, $paramNames);
            $params = [];

            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index] ?? null;
            }

            return $params;
        }

        return false;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getAction(): Closure|array|string
    {
        return $this->action;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }
}
