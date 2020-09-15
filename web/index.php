<?php

/**
 * @author StepanovM
 *
 * Main application entry point
 * init global application settings
 */

/** autoload */
require __DIR__ . '/../Lynxx/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

/** System configuration */
error_reporting(E_ALL);
if (version_compare(phpversion(), '7.2', '<') == true) { die ('PHP7.2 Only'); }
date_default_timezone_set('Europe/Moscow');
//set_exception_handler('\app\core\Utils::handleException');

session_start();

$routes = (new \Lynxx\RoutesMap())->init()->getRoutes();
echo \Lynxx\Utils::debugObj($routes);

$config = new \app\config\Config();
echo \Lynxx\Utils::debugObj((new \app\config\Config())->test);


$request = \Lynxx\Container\Container::get(\Lynxx\Request\Request::class);
echo \Lynxx\Utils::debugObj($request);

$request->test = 'privet';

$request_new = \Lynxx\Container\Container::get(\Lynxx\Request\Request::class);
echo \Lynxx\Utils::debugObj($request_new);