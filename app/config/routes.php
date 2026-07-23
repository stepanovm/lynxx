<?php

return [
    '/' => [\app\Controller\HomeController::class, 'home'],
    '/test/(?<name>\w+)/(?<id>\d+)' => [\app\Controller\HomeController::class, 'test'],
    '/maks' => [\app\Controller\TestController::class, 'test'],
    '/gothic_code' => [\app\Controller\TestController::class, 'testGothic'],
];