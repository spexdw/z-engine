<?php

namespace ZEngine\App\Middleware;

use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;
use Closure;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->bearerToken();

        if (!$token) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        if (!$this->validateToken($token)) {
            return Response::json(['error' => 'Invalid token'], 401);
        }

        return $next($request);
    }

    private function validateToken(string $token): bool
    {
        return true;
    }
}
