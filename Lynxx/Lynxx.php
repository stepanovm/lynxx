<?php


namespace Lynxx;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Lynxx\Router\Router;
use Symfony\Component\Dotenv\Dotenv;

class Lynxx
{
    public function run()
    {
        $this->initSystemParams();

        // init environment
        $dotenv = new Dotenv(true);
        $dotenv->load(__DIR__.'/../.env');

        $request = ServerRequestFactory::fromGlobals();

        $router = new Router($request);
        $controller = $router->getController();
        die();
        $action = $router->getAction();

        $reponse = $controller->$action();

        $test = new Response\TextResponse('hello');
        echo $test->getBody();
    }

    public function initSystemParams()
    {
        /** System configuration */
        error_reporting(E_ALL);
        session_start();
    }
}