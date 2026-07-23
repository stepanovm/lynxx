<?php


namespace app\model\core;


class MyPdoStatement extends \PDOStatement
{
    public static $db_requests = [];

    public function execute($input_parameters = null)
    {
        self::$db_requests[] = [
            'query' => $this->queryString,
            'values' => $input_parameters
        ];
        return parent::execute($input_parameters);
    }
}