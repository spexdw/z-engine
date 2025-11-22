<?php

namespace ZEngine\App\Controllers;

use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;

class HomeController
{
    public function index(Request $request): Response
    {
        return json([
            'message' => 'Home Controller',
            'method' => $request->method(),
            'path' => $request->path(),
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        return json([
            'message' => 'Show method',
            'id' => $id,
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $request->all();

        return json([
            'message' => 'Data stored',
            'data' => $data,
        ], 201);
    }
}
