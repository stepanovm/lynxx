<?php

namespace app\model\core;


trait TraitDefferedCollection
{
    /** @var \PDOStatement  */
    private $stmt;
    /** @var array  */
    private $stmt_values;
    /** @var bool  */
    private $run = false;

    /**
     * TraitDefferedCollection constructor.
     * @param Mapper $mapper
     * @param \PDOStatement $stmt
     * @param array $stmt_values
     */
    public function __construct(Mapper $mapper, \PDOStatement $stmt, array $stmt_values) {
        parent::__construct(null, $mapper);
        $this->stmt = $stmt;
        $this->stmt_values = $stmt_values;
    }


    function notifyAccess() {
        if(!$this->run){
            $this->stmt->execute($this->stmt_values);
            $this->raw = $this->stmt->fetchAll();
            $this->total = count( $this->raw );
        }
        $this->run = true;
    }
}