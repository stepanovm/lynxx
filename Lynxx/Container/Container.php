<?php


namespace Lynxx\Container;


use Lynxx\Utils;

class Container
{
    /**
     * @var array
     */
    private static $singltones = [];

    private function __construct()
    {
    }

    /**
     * @param string $class
     * @return mixed
     * @throws \ReflectionException
     */
    public static function get(string $class)
    {
        if (array_key_exists($class, self::$singltones)) {
            return self::$singltones[$class];
        }

        $refl = new \ReflectionClass($class);
        $classArgs = [];

        if ($refl->hasMethod('__construct')){
            $refl_args = $refl->getMethod('__construct')->getParameters();
            if (isset($refl_args)) {
                foreach ($refl_args as $arg) {
                    $argClassName = $arg->getClass();
                    if (is_null($argClassName)) {
                        throw new \ReflectionException('unknown argument type ' . $arg);
                    }
                    $classArgs[] = self::get($argClassName->getName());
                }
            }
        }

        $obj = new $class(...$classArgs);
        self::$singltones[$class] = $obj;
        return $obj;
    }
}