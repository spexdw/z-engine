<?php

namespace ZEngine\App\Controllers;

use ZEngine\Core\Http\Request;
use ZEngine\Core\Http\Response;
use ZEngine\App\Models\WelcomeModel;

class WelcomeController
{
    private WelcomeModel $welcomeModel;

    public function __construct()
    {
        $this->welcomeModel = new WelcomeModel();
    }

    public function showWelcome(): Response
    {
        $something = $this->welcomeModel->getSomething();

        return view('welcome', [
            'something' => $something,
            'version' => '1.0.2',
            'services_count' => '9'
        ]);
    }

}
