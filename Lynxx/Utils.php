<?php


namespace Lynxx;


class Utils
{

    /**
     * @param mixed $object
     * @return string
     */
    public static function debugObj($object): string
    {
        return '<pre>' . print_r($object, TRUE) . '</pre>';
    }

}