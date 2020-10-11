<?php


namespace Lynxx;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Lynxx\Router\RouteNotFoundException;
use Lynxx\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;

class Lynxx
{
    public function run()
    {
        $this->initSystemParams();

        $request = ServerRequestFactory::fromGlobals();

        /** @var array $routes just require file from config's path */
        require __DIR__ . '/../app/config/routes.php';

        $router = new Router($request, $routes);

        $controller = $router->getController();
        $action = $router->getAction();
        $queryAttributes = $router->getAttributes();

        foreach ($queryAttributes as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        $controller = new $controller();
        if(!$controller instanceof AbstractController){
            throw new RouteNotFoundException('bad controller class');
        }

        /** @var ResponseInterface $response */
        $response = $controller->$action($request);

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