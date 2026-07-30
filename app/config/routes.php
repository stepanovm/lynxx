<?php

return [
    '/' => [\app\Controller\HomeController::class, 'home'],
    '/test/(?<name>\w+)/(?<id>\d+)' => [\app\Controller\HomeController::class, 'test'],
    '/request/getInitContent' => [\app\Controller\request\DynamicLoadController::class, 'loadDynamicComponents'],
    '/auth/auth_by_pass' => [\app\Controller\request\AuthController::class, 'authByPass'],
    '/maks' => [\app\Controller\TestController::class, 'test'],
    '/gothic_code' => [\app\Controller\TestController::class, 'testGothic'],
];