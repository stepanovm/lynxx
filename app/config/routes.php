<?php

return [
    '/' => [\app\Controller\HomeController::class, 'home'],
    '/test/(?<name>\w+)/(?<id>\d+)' => [\app\Controller\HomeController::class, 'test'],
];