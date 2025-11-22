# ZEngine

[![CI](https://github.com/spexdw/z-engine/workflows/CI/badge.svg)](https://github.com/spexdw/z-engine/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A lightweight PHP framework. No bloat, just the essentials you actually need.

## Why?

I got tired of complex frameworks with thousands of files. ZEngine is simple - you can read the entire source code in an afternoon and understand exactly what's happening.

## What's Included

- **Routing** - Map URLs to functions, add parameters, group routes
- **Middleware** - Protect routes, handle auth, whatever you need
- **Database** - Query builder that doesn't get in your way
- **Services** - Session, cookies, cache, validation, logging etc.
- **Dependency Injection** - Automatic, no XML configs
- **Error Pages** - Custom error handling with nice debug screens

## Requirements

- PHP 8.1+
- Composer
- That's it

## Installation

Create a new project:

```bash
composer create-project spexdw/z-engine my-project
cd my-project
```

Or clone it directly:

```bash
git clone https://github.com/spexdw/z-engine.git my-project
cd my-project
composer install
```

## Quick Start

1. Copy `.env.example` to `.env` and set your config
2. Edit `app/routes.php` to add your routes

```php
$router->get('/hello/{name}', function ($name) {
    return json(['message' => "Hello, $name!"]);
});
```

That's it. No generators, no boilerplate, just write code.

## Routing

```php
// Basic routes
$router->get('/users', function () {
    return json(['users' => []]);
});

$router->post('/users', function (Request $request) {
    $data = $request->json();
    return json(['created' => $data], 201);
});

// URL parameters
$router->get('/users/{id}', function ($id) {
    return json(['user_id' => $id]);
});

// Protect routes with middleware
$router->get('/admin', function () {
    return json(['secret' => 'data']);
})->middleware(AdminMiddleware::class);

// Group routes
$router->group(['prefix' => '/api'], function ($router) {
    $router->get('/users', fn() => json([]));
    $router->get('/posts', fn() => json([]));
});
```

## Database

```php
// Query builder
$users = db()->table('users')
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->get();

// Raw queries
$results = db()->query('SELECT * FROM users WHERE id = ?', [1]);

// Insert/Update/Delete
db()->insert('users', ['name' => 'John', 'email' => 'john@example.com']);
db()->update('users', ['status' => 'active'], ['id' => 1]);
db()->delete('users', ['id' => 1]);

// Transactions
db()->beginTransaction();
try {
    db()->insert('users', ['name' => 'John']);
    db()->commit();
} catch (Exception $e) {
    db()->rollback();
}
```

## Services

```php
// Session
session()->set('user_id', 123);
$userId = session()->get('user_id');

// Cache (5 minutes)
cache()->set('key', 'value', 300);
$value = cache()->get('key');

// Cookies
cookie()->set('theme', 'dark', time() + 3600);
$theme = cookie()->get('theme');

// Validation
$rules = ['email' => 'required|email', 'age' => 'min:18'];
$isValid = validator()->validate($data, $rules);

// Logging
logger()->error('Something broke', ['context' => 'details']);
```

## Middleware

Create a middleware in `app/Middleware`:

```php
class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->header('Authorization')) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
```

Use it:

```php
$router->get('/dashboard', function () {
    return json(['data' => 'secret']);
})->middleware(AuthMiddleware::class);
```

## Custom Services

Add your service to `core/Providers.php`:

```php
private static function registerMyService(Container $container): void
{
    $container->singleton('myservice', function () {
        return new MyService();
    });
}
```

Then call `self::registerMyService($container);` in the `register()` method.

Use it anywhere:

```php
$result = app('myservice')->doSomething();
```

## Error Handling

Set `APP_DEBUG=true` in `.env` for detailed error pages with code snippets.

Set `APP_DEBUG=false` for production to show clean error pages.

All errors are logged to `storage/logs/error.log`.

## Contributing

Found a bug? Want a feature? Open an issue or PR. Keep it simple.

## License

MIT. Do whatever you want with it.

## Credits

Built by [spexdw](https://github.com/spexdw) because existing frameworks were too complicated :p
