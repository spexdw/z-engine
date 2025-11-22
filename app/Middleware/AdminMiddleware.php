<?php

namespace ZEngine\App\Middleware;

use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;
use Closure;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->header('X-Admin-Token') ?? $request->input('admin_token');

        if (!$token || $token !== env('ADMIN_TOKEN', 'secret-admin-token')) {
            return Response::json([
                'error' => 'Unauthorized',
                'message' => 'Admin token required'
            ], 401);
        }

        return $next($request);
    }
}
