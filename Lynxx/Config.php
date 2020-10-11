<?php


namespace Lynxx;


class Config {
    const APP_MODE_DEV = 'DEV';
    const APP_MODE_PROD = 'PROD';

    private static $config;

    private function __construct() {}

    public static function get($key)
    {
        if(!isset(self::$config)){
            $conf = [];
            require_once(__DIR__ . '/../app/config/config.php');
            self::$config = $conf;
        }
        return self::$config[$key];
    }
}