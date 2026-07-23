<?php


namespace app\model\core;


class QueryBuilder
{
    /** @var string $query */
    private $query;

    /** @var QueryTable[] */
    private $tables = [];

    /** @var string */
    private $fromAlias;

    private $filters = [];
    /** @var string */
    private $sort = null;


    /**
     * @return $this
     */
    public function select()
    {
        $this->query = 'SELECT ';
        return $this;
    }

    /**
     * @param Mapper $mapper
     * @param string $alias
     * @param array|null $columns
     * @return $this
     */
    public function from(Mapper $mapper, string $alias, array $columns = null): QueryBuilder
    {
        $this->fromAlias = $alias;
        $this->tables[$alias] = new QueryTable($mapper, $alias, $columns);
        return $this;
    }

    /**
     * @param Mapper $mapper
     * @param string $alias
     * @param string $targetAlias
     * @param array|null $columns
     * @return $this
     */
    public function join(Mapper $mapper, string $alias, string $targetAlias, ?array $columns = null): QueryBuilder
    {
        $this->tables[$alias] = new QueryTable($mapper, $alias, $columns, $targetAlias, '');
        return $this;
    }

    public function leftjoin(Mapper $mapper, string $alias, string $targetAlias, ?array $columns = null): QueryBuilder
    {
        $this->tables[$alias] = new QueryTable($mapper, $alias, $columns, $targetAlias, 'LEFT');
        return $this;
    }

    public function rightjoin(Mapper $mapper, string $alias, string $targetAlias, ?array $columns = null): QueryBuilder
    {
        $this->tables[$alias] = new QueryTable($mapper, $alias, $columns, $targetAlias, 'RIGHT');
        return $this;
    }


    /**
     * @return string
     */
    public function create(): string
    {
        $fromTable = $this->tables[$this->fromAlias];
        $queryColumns = implode(', ', $this->getColumns());

        $this->query .= $queryColumns
            . ' FROM ' . $fromTable->getName() . ' ' . $fromTable->alias;

        // if request has joined tables:
        if (count($this->tables) > 1) {
            foreach ($this->tables as $joinTable) {
                if ($joinTable->alias == $fromTable->alias) {
                    continue;
                }

                $linkString = $this->getLinkString($joinTable);

                $this->query .= ' ' . $joinTable->joinType . ' JOIN ' . $joinTable->getName() . ' ' . $joinTable->alias . ' ON ' . $linkString;
            }
        }

        if(!empty($this->filters)){
            $this->query .= ' WHERE';
            foreach ($this->filters as $filter) {
                $this->query .=  ' ' . $filter;
            }
        }

        if (!is_null($this->sort)) {
            $this->query .=  ' ORDER BY ' . $this->sort;
        }

        return $this->query;
    }


    public function where(?string $string): QueryBuilder
    {
        if(!is_null($string)) {
            $this->filters[] = $string;
        }
        return $this;
    }

    public function orderBy(string $string, ?string $direction = 'ASC'): QueryBuilder
    {
        $this->sort = $string . ' ' . $direction;
        return $this;
    }



    /**
     * @param QueryTable $joinTable
     * @return string|null
     */
    private function getLinkString(QueryTable $joinTable): ?string
    {
        $targetProperties = $this->tables[$joinTable->targetAlias]->mapper->getProperties();

        $joinEntityClass = $joinTable->mapper->targetEntity();
        foreach ($targetProperties['columns'] as $column) {
            if (isset($column['targetEntity']) && $column['targetEntity'] == $joinEntityClass) {
                return $joinTable->alias . '.id' . ' = ' . $joinTable->targetAlias . '.' . $column['field_name'];
            }
        }

        foreach ($targetProperties['relations'] as $relation) {
            if (isset($relation['targetEntity']) && $relation['targetEntity'] == $joinEntityClass) {
                return $joinTable->alias . '.' . $relation['byColumn'] . ' = ' . $joinTable->targetAlias . '.' . 'id ';
            }
        }

        throw new \DomainException('LinkColumnName not founded');
    }


    /**
     * @return array
     */
    private function getColumns()
    {
        $columns = [];
        foreach ($this->tables as $table) {
            $columns = array_merge($columns, $table->columns);
        }
        return $columns;
    }

}