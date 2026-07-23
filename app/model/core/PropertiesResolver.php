<?php


namespace app\model\core;


use Lynxx\Lynxx;

class PropertiesResolver
{
    private dbTypeResolver $dbTypeResolver;

    /**
     * @param dbTypeInterface $dbType
     */
    public function __construct(dbTypeResolver $dbTypeResolver)
    {
        $this->dbTypeResolver = $dbTypeResolver;
    }


    public function getValuesMap(DomainObject $obj)
    {
        $columns = $obj->mapper()->getProperties()['columns'];

        $fields = [];
        $values = [];

        $this->resolveColumns($columns, $obj, $fields, $values);

        return ['fields' => $fields, 'values' => $values];
    }

    private function resolveColumns(array $columns, object $obj, array &$fields, array &$values): void
    {
        foreach ($columns as $property => $column) {

            $getter = 'get' . ucfirst($property);

            if (isset($column['embeddedClass']) && !is_null($obj->$getter())) {
                $this->resolveColumns($column['columns'], $obj->$getter(), $fields, $values);
                continue;
            }

            if (is_null($obj->$getter())) {
                continue;
            }

            $fields[] = $column['field_name'];

            if(array_key_exists('targetEntity', $column) && $obj->$getter() instanceof DomainObject){
                $value = $obj->$getter()->getId();
            } else if (array_key_exists('type', $column)) {
                $value = $this->dbTypeResolver->resolveToDatabase($column['type'], $obj->$getter());
            } else {
                $value = $obj->$getter();
            }

            $values[':' . $column['field_name']] = $value;
        }
    }

    public function getFieldsList(array $mapperProperties): array
    {
        $fields = [];
        $this->resolveColumnsFields($mapperProperties['columns'], $fields);
        return $fields;
    }

    private function resolveColumnsFields(array $columns, array &$fields): void
    {
        foreach ($columns as $column) {
            if (isset($column['embeddedClass'])) {
                $this->resolveColumnsFields($column['columns'], $fields);
                continue;
            }
            $fields[] = $column['field_name'];
        }
    }

    public function isColumnCorrect(string $column, array $properties)
    {
        return in_array($column, $this->getFieldsList($properties));
    }
}