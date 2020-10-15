<?php


namespace Lynxx;

use Laminas\Diactoros\ServerRequestFactory;
use Lynxx\Container\Container;
use Lynxx\Router\RouteNotFoundException;
use Lynxx\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Dotenv\Dotenv;

class Lynxx
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $this->initSystemParams();

        /** @var Router $router */
        $router = $this->container->get(Router::class);

        $request = ServerRequestFactory::fromGlobals();

        $controllerClass = $router->getControllerClass();
        $actionName = $router->getActionName();
        $queryAttributes = $router->getAttributes();

        foreach ($queryAttributes as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }
        $this->container->set('request', $request);

        $controller = $this->container->get($controllerClass);
        if(!$controller instanceof AbstractController){
            throw new RouteNotFoundException('bad controller class');
        }

        /** @var ResponseInterface $response */
        $response = $controller->$actionName();

        echo $response->getBody();
    }

    public function initSystemParams()
    {
        /** System configuration */
        error_reporting(E_ALL);
        session_start();

        $dotenv = new Dotenv(true);
        $dotenv->load(__DIR__.'/../.env');
    }
}