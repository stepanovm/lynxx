<?php

use Laminas\Diactoros\ServerRequestFactory;
use Lynxx\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

return [
    'routes' => function () {
        return $routes = require 'routes.php';
    },
    Router::class => function (ContainerInterface $container) {
        return new Router($container->get('request'), $container->get('routes'));
    },
    ContainerInterface::class => function (ContainerInterface $container) {
        return $container;
    },
    ServerRequestInterface::class => function (ContainerInterface $container) {
        return $container->get('request');
    },
];