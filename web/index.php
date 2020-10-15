<?php

/**
 * @author StepanovM
 *
 * Main application entry point
 */

require __DIR__ . '/../vendor/autoload.php';
$container = new \Lynxx\Container\Container();

$app = $container->get(\Lynxx\Lynxx::class);

$app->run();