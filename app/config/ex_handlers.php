<?php

use app\Controller\Errors\SiteErrorController;
use Lynxx\Container\ContainerException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    ContainerException::class => function (ContainerInterface $container, Throwable $ex) {
        $logger = $container->get(LoggerInterface::class);
        $logger->error($ex->getMessage(), ['throwable' => $ex]);
        ($container->get(SiteErrorController::class))->run($ex);
    },
];