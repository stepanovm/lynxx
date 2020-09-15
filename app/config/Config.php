<?php


namespace app\config;


class Config
{
    private $config = [
        'db' => array (
            'dbname' => 'lynxx_db',
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'sqlType' => 'mysql'
        ),
        'test' => 123
    ];

    /**
     * @param string $name
     * @return string|array
     */
    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }
}