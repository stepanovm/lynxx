<?php

/**
 * @author StepanovM
 *
 * Main application entry point
 * init global application settings
 */

/** autoload */
require __DIR__ . '/../vendor/autoload.php';

/** System configuration */
error_reporting(E_ALL);

session_start();

$container = new \Lynxx\Container\Container();
$container->set('helloMessage', 'Hello, Maks!');

$response = (new \Zend\Diactoros\Response\HtmlResponse('Hello from Zend'))
    ->withHeader('X-Developer', 'StepanovM');

$em = new

header('HTTP/1.0 ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase());
foreach ($response->getHeaders() as $name => $values) {
    header($name . ':' . implode(',' , $values));
}
echo $response->getBody();

echo $container->get('helloMessage');