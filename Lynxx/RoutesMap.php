<?php


namespace Lynxx;


class RoutesMap
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @return $this
     * @throws \Exception
     */
    public function init(): RoutesMap
    {
        $routes = [];
        include_once __DIR__ . '/../app/config/routes.php';
        if (!is_array($routes)) {
            throw new Ex('unable to read route');
        }
        $this->routes = $routes;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}