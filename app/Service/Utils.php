<?php

namespace app\Service;

class Utils
{
    public static function debugObj($object)
    {
        return '<pre>' . print_r($object, TRUE) . '</pre>';
    }
}