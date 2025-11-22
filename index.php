<?php

require_once __DIR__ . '/vendor/autoload.php';

use ZEngine\Core\ZEngine;

$app = ZEngine::init();

require_once __DIR__ . '/app/routes.php';

$app->run();
