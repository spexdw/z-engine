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

        // create event
        event()->listen('something_dat');

        // event function
        event()->listen('something_dat', function ($smthng) {
            logger()->info("Something data: $smthng");
        });

        // event dispatch
        event()->dispatch('something_dat','SOME_EVENT_DATA');

        return view('welcome', [
            'something' => $something,
        ]);
    }

}
