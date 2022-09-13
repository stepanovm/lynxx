<?php


namespace app\model\core;


class SqlResultsHelper
{
    private $collections = [];

    public function getEntityValues(Mapper $mapper, array $dbRow, string $alias)
    {
        $tableData = [];
        if (isset($dbRow[$alias . '_id'])) {
            foreach ($mapper->getProperties()['columns'] as $mtColumn) {
                $tableData[$mtColumn['field_name']] = $dbRow[$alias . '_' . $mtColumn['field_name']];
            }
        }
        return $tableData;
    }

    /**
     * @param Mapper $mapper mapper of target
     * @param array $dbRow  entity data
     * @param string $alias alias
     * @param array $targetArray reference to the source array (it will be changed)
     * @param string $targetKey
     * @return void
     */
    public function parseCollectionRow(Mapper $mapper, array $dbRow, string $alias, array &$targetArray, string $targetKey)
    {
        $tableData = [];
        if (isset($dbRow[$alias . '_id'])) {
            if(isset($this->collections[$targetKey]) && array_key_exists($dbRow[$alias . '_id'], $this->collections[$targetKey])) {
                return;
            }
            $this->collections[$targetKey][$dbRow[$alias . '_id']] = true;
            foreach ($mapper->getProperties()['columns'] as $mtColumn) {
                $tableData[$mtColumn['field_name']] = $dbRow[$alias . '_' . $mtColumn['field_name']];
            }
            $targetArray[$targetKey][] = $tableData;
        } else {
            $targetArray[$targetKey] = [];
        }
    }
}