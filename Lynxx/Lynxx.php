<?php


namespace Lynxx;

use Lynxx\Auth\Auth;
use Lynxx\Container\Container;
use Lynxx\Router\RouteNotFoundException;
use Lynxx\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;

class Lynxx
{
    private static ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        self::$container = $container;
    }

    public static function getContainer(): ContainerInterface
    {
        if(!isset(self::$container)){
            self::$container = new Container();
        }
        return self::$container;
    }

    public function run()
    {
        $this->initSystemParams();

        /** @var Router $router */
        $router = self::$container->get(Router::class);

        $controllerClass = $router->getControllerClass();
        $actionName = $router->getActionName();
        $queryAttributes = $router->getAttributes();


        $request = $router->getRequest();
        foreach ($queryAttributes as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }
        self::$container->set(RequestInterface::class, $request);


        $controller = self::$container->get($controllerClass);
        if (!$controller instanceof AbstractController) {
            throw new RouteNotFoundException('bad controller class');
        }

        $response = $controller->$actionName();

        if ($response instanceof ResponseInterface) {
            foreach ($response->getHeaders() as $k => $values) {
                foreach ($values as $v) {
                    header(sprintf('%s: %s', $k, $v), false);
                }
            }
            echo $response->getBody();
        }

    }

    public function initSystemParams()
    {
        /** System configuration */
        error_reporting(E_ALL);
        set_exception_handler('\Lynxx\Exception\ExHandler::handle');
        date_default_timezone_set('Europe/Moscow');
        session_start();

        $dotenv = new Dotenv(true);
        $dotenv->load(__DIR__ . '/../.env');
    }

    public static function Auth(): Auth
    {
        return self::getContainer()->get(Auth::class);
    }

    public static function debugPrint($data): ?string
    {
        return '<pre>'.print_r($data, true).'</pre>';
    }
}