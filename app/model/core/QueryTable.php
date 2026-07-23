<?php


namespace app\model\core;


class QueryTable
{
    /** @var Mapper */
    public $mapper;
    /** @var array */
    public $columns;
    /** @var null|string */
    public $alias;
    /** @var null|string */
    public $targetAlias;
    /** @var string */
    public $joinType;

    /**
     * QueryTable constructor.
     * @param Mapper $mapper
     * @param string $alias
     * @param array|null $columns
     * @param string|null $targetAlias
     * @param string|null $joinType
     */
    public function __construct(Mapper $mapper, string $alias, ?array $columns = null, ?string $targetAlias = null, ?string $joinType = '')
    {
        $this->alias = $alias;
        $this->mapper = $mapper;
        $this->joinType = $joinType;
        $this->columns = $this->getColumns($columns ?? $mapper->getFieldsList(), $alias);
        $this->targetAlias = $targetAlias;
    }


    private function getColumns($columns, $alias)
    {
        $aliasColumns = array_map(
            function ($column) use ($alias) {
                return $alias . '.' . $column . ' as ' . $alias . '_' . $column;
            },
            $columns
        );
        return array_values($aliasColumns);
    }

    public function getName()
    {
        return $this->mapper->getTable();
    }


}