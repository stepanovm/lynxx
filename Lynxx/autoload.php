<?php

namespace Lynxx;

spl_autoload_register('Lynxx\autoLoad');

/**
 * autoload classes
 * @param string $className
 */
function autoLoad($className){

    $sep = DIRECTORY_SEPARATOR;
    $className = ltrim($className, '\\');   // delete first '\' symbol if exist

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $classPath = __DIR__ . '/..' . $sep . str_replace('\\', $sep, $namespace) . $sep;
        require_once($classPath . $className.'.php');
    }
}