<?php


namespace app\config;


class RoutesMap
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @var self
     */
    private static $instance;

    public function __construct()
    {
        $this->routes = [
            '/' => 'ControllerHome:index',
        ];
    }

    /**
     * @return static
     */
    static function instance(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return (self::instance())->routes;
    }
}