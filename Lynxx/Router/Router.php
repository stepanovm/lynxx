<?php


namespace Lynxx\Router;


use Psr\Http\Message\RequestInterface;

class Router
{
    private $request;
    private $routesMap;

    /**
     * Router constructor.
     * @param $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        /** @var array $routes just require file from config's path */
        require_once __DIR__ . '/../../app/config/routes.php';
        $this->routesMap = $routes;

        $expr_pattern = '~^'.$pattern.'$~';
    }

    public function getController()
    {
        $path = $this->request->getUri()->getPath();

    }

    private function resolveRoutes()
    {

    }



}