<?php

$routes = [
    '/' => [\app\Controller\HomeController::class, 'home'],
    '/test/(<?id>\d+)' => [\app\Controller\HomeController::class, 'test'],
];