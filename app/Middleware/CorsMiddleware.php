<?php

namespace ZEngine\App\Middleware;

use ZEngine\Core\Http\Request;
use Closure;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        if ($request->method() === 'OPTIONS') {
            $response->setStatusCode(200);
            return $response;
        }

        return $response;
    }
}
