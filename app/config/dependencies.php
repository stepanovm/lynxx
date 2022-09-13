<?php

use app\Controller\Errors\SiteErrorController;
use bin\Command\AppBuild\Compressor\CssCompressor;
use bin\Command\AppBuild\Compressor\JsCompressor;
use Laminas\Diactoros\ServerRequestFactory;
use Lynxx\Container\ContainerException;
use Lynxx\DB;
use Lynxx\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

return [
    'config' => function() { return $config = require 'config.php'; },
    'routes' => function () { return $routes = require 'routes.php'; },
    'mappersMap' => require 'mappers_map.php',
    'typesMap' => require 'dbTypes/types_map.php',
    'default_request' => ServerRequestFactory::fromGlobals(),
    'compressor_Js' => function (ContainerInterface $container) {
        return new JsCompressor();
    },
    'compressor_Css' => function (ContainerInterface $container) {
        return new CssCompressor();
    },

    Router::class => function (ContainerInterface $container) {
        return new Router($container->get('default_request'), $container->get('routes'));
    },

    ServerRequestInterface::class => function (ContainerInterface $container) {
        return $container->get(RequestInterface::class);
    },

    ContainerInterface::class => function (ContainerInterface $container) {
        return $container;
    },

    PDO::class => function (ContainerInterface $container) {
        return DB::instance();
    },
    CookieManagerInterface::class => function (ContainerInterface $container) {
        return $container->get(\Lynxx\Auth\CookieManager::class);
    },
    LoggerInterface::class => function(ContainerInterface $container) {
        return $container->get(\Lynxx\Logger::class);
    },

    'exceptionDependencies' => [
        ContainerException::class => function (ContainerInterface $container, Throwable $ex) {
            $logger = $container->get(LoggerInterface::class);
            $logger->error($ex->getMessage(), ['throwable' => $ex]);
           ($container->get(SiteErrorController::class))->run($ex);
        },
    ],
];