<?php

namespace ZEngine\App\Middleware;

use ZEngine\Core\Http\Request;
use Closure;

class GuestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Wohoooooo'
            ], 200);
        }

        return $next($request);
    }
}

