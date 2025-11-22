<?php

use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;

$router = router();

$router->get('/', function (Request $request) {
    $version = app()->version();
    return view('welcome', [
        'version' => $version,
        'services_count' => app()->getContainer()->count()
    ]);
});

// =====================================================
// Route Examples
// =====================================================

// Basic GET route
// $router->get('/hello', function () {
//     return json(['message' => 'Hello World!']);
// });

// POST route
// $router->post('/users', function (Request $request) {
//     $data = $request->json();
//     return json(['message' => 'User created', 'data' => $data], 201);
// });

// Route with parameter
// $router->get('/users/{id}', function (Request $request, string $id) {
//     return json(['user_id' => $id, 'name' => 'User ' . $id]);
// });

// Multiple parameters
// $router->get('/posts/{post}/comments/{comment}', function ($post, $comment) {
//     return json(['post_id' => $post, 'comment_id' => $comment]);
// });

// =====================================================
// Middleware Usage
// =====================================================

// Single route with middleware
// $router->get('/admin', function () {
//     return json(['message' => 'Admin Panel']);
// })->middleware(\ZEngine\App\Middleware\AdminMiddleware::class);

// =====================================================
// Route Groups
// =====================================================

// Group with prefix
// $router->group(['prefix' => '/api'], function ($router) {
//     $router->get('/users', function () {
//         return json(['users' => []]);
//     });
//     $router->get('/posts', function () {
//         return json(['posts' => []]);
//     });
// });

// Group with middleware
// $router->group(['prefix' => '/admin', 'middleware' => [\ZEngine\App\Middleware\AdminMiddleware::class]], function ($router) {
//     $router->get('/dashboard', function () {
//         return json(['message' => 'Admin Dashboard']);
//     });
//     $router->get('/users', function () {
//         return json(['users' => ['John', 'Jane']]);
//     });
// });

// =====================================================
// HTTP Methods
// =====================================================

// GET, POST, PUT, PATCH, DELETE
// $router->get('/resource', function () { return json(['method' => 'GET']); });
// $router->post('/resource', function () { return json(['method' => 'POST']); });
// $router->put('/resource/{id}', function ($id) { return json(['method' => 'PUT', 'id' => $id]); });
// $router->patch('/resource/{id}', function ($id) { return json(['method' => 'PATCH', 'id' => $id]); });
// $router->delete('/resource/{id}', function ($id) { return json(['method' => 'DELETE', 'id' => $id]); });

// =====================================================
// Response Types
// =====================================================

// JSON response
// $router->get('/json', function () {
//     return json(['key' => 'value']);
// });

// View response
// $router->get('/view', function () {
//     return view('welcome', ['name' => 'John']);
// });

// Redirect
// $router->get('/redirect', function () {
//     return redirect('/');
// });

// Plain text
// $router->get('/text', function () {
//     return Response::make('Plain text response');
// });

// =====================================================
// Request Data
// =====================================================

// Get input data
// $router->post('/submit', function (Request $request) {
//     $name = $request->input('name');
//     $email = $request->input('email', 'default@example.com');
//     $all = $request->all();
//     return json(['received' => $all]);
// });

// Get JSON data
// $router->post('/api/data', function (Request $request) {
//     $data = $request->json();
//     return json(['received' => $data]);
// });

// File upload
// $router->post('/upload', function (Request $request) {
//     $file = $request->file('avatar');
//     return json(['filename' => $file['name'] ?? 'No file']);
// });

// =====================================================
// Services Usage
// =====================================================

// Database
// $router->get('/db-example', function () {
//     $users = db()->query('SELECT * FROM users LIMIT 10');
//     return json(['users' => $users]);
// });

// Session
// $router->get('/session-example', function () {
//     session()->set('user_id', 123);
//     session()->flash('message', 'Success!');
//     return json(['user_id' => session()->get('user_id')]);
// });

// Cookie
// $router->get('/cookie-example', function () {
//     cookie()->set('theme', 'dark', time() + 3600);
//     return json(['theme' => cookie()->get('theme')]);
// });

// Cache
// $router->get('/cache-example', function () {
//     cache()->set('key', 'value', 300);
//     return json(['cached' => cache()->get('key')]);
// });
