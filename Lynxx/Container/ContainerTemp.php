<?php


namespace Lynxx\Container;


use Lynxx\Utils;

class ContainerTemp
{
    /** @var array  */
    private static $singltones = [];

    private function __construct()
    {
    }

    /**
     * @param string $class
     * @return mixed
     * @throws \ReflectionException
     */
    public static function get(string $id)
    {
        if (array_key_exists($id, self::$singltones)) {
            return self::$singltones[$id];
        }

        $refl = new \ReflectionClass($id);
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