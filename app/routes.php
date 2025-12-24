<?php

use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;
use ZEngine\App\Controllers\WelcomeController;
use ZEngine\App\Middleware\GuestMiddleware;

$router = router();

if (env('MAINTENANCE_MODE', 0)) {
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $whitelistedIps = env('WHITELISTED_IPS', []);

    if (!in_array($clientIp, $whitelistedIps)) {
        http_response_code(503);
        include __DIR__ . '/Views/errors/maintenance.php';
        exit;
    }
}

$router->group(['middleware' => [GuestMiddleware::class]], function ($router) {
    $router->get('/', [WelcomeController::class, 'showWelcome']);
    $router->get('/welcome', [WelcomeController::class, 'showWelcome']);
});

$router->get('/hi/{name}', function ($name) {
    return json([
        'success' => true,
        'message' => "Hello, $name!"
    ]);
});