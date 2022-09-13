<?php

namespace app\model\core;


use Lynxx\Lynxx;
use PDO;
use PDOStatement;

/**
 * Class Mapper
 *
 * use <b>IdentityMap</b> pattern
 *
 * @package app\model\mapper
 */
abstract class Mapper
{
    protected static PDO $PDO;
    protected PDOStatement $findStmt;
    protected PDOStatement $findAllStmt;
    protected DomainObjectFactory $domainObjectFactory;
    protected PropertiesResolver $propertiesResolver;

    /**
     * Mapper constructor.
     * @param DomainObjectFactory $domainObjectFactory
     * @param PDO $pdo
     * @param PropertiesResolver $propertiesResolver
     */
    public function __construct(DomainObjectFactory $domainObjectFactory, PDO $pdo, PropertiesResolver $propertiesResolver)
    {
        $this->propertiesResolver = $propertiesResolver;
        $this->domainObjectFactory = $domainObjectFactory;
        self::$PDO = $pdo;
    }

    /**
     * @param int $id
     * @return DomainObject instance of DomainObject
     * @throws \ReflectionException
     */
    public function find(int $id): ?DomainObject
    {
        $old = $this->domainObjectFactory->getFromMap($this->targetEntity(), $id);
        if ($old) {
            return $old;
        }

        $this->findStmt()->execute(['id' => $id]);
        $array = $this->findStmt()->fetch();
        $this->findStmt()->closeCursor();
        if (!is_array($array)) {
            return null;
        }
        if (!isset($array['id'])) {
            return null;
        }
        return $this->createObject($array);
    }

    /**
     * @return Collection
     */
    public function findAll()
    {
        $this->findAllStmt()->execute();
        $raw = $this->findAllStmt()->fetchAll();
        return $this->getCollection($raw);
    }

    /**
     * @return array
     */
    public function findAllAsArray()
    {
        $this->findAllStmt()->execute();
        $raw = $this->findAllStmt()->fetchAll();
        return $raw;
    }

    /**
     * Load saved or create new domain object (use <b>IdentityMap</b> pattern)
     *
     * @param array $array row from db
     * @return DomainObject
     */
    public function createObject(array $array)
    {
        return $this->domainObjectFactory->createObject($this->targetEntity(), $this->getProperties(), $array);
    }


    /**
     * @param string $column
     * @param $value
     * @return DomainObject|null
     */
    public function findOneBy(string $column, $value): ?DomainObject
    {
        // need to make sure that the field $column actually exists at columns list
        if (!$this->propertiesResolver->isColumnCorrect($column, $this->getProperties())) {
            throw new \DomainException('unknown field');
        }

        $stmt = self::$PDO->prepare('SELECT * FROM ' . $this->getTable() . ' WHERE ' . $column . ' = :value');
        $stmt->execute([':value' => $value]);
        $response_arr = $stmt->fetch();
        if(!is_array($response_arr)){
            return null;
        }
        $obj = $this->createObject($response_arr);
        return $obj instanceof DomainObject ? $obj : null;
    }



    /**
     * @param $value
     * @param string $column
     * @return Collection
     */
    public function findManyBy($value, string $column): Collection
    {
        // need to make sure that the field $column actually exists at columns list
        if (!$this->propertiesResolver->isColumnCorrect($column, $this->getProperties())) {
            throw new \DomainException('unknown field');
        }

        $stmt = self::$PDO->prepare('SELECT * FROM ' . $this->getTable() . ' WHERE ' . $column . ' = :value');
        $stmt->execute([':value' => $value]);
        return $this->getCollection($stmt->fetchAll());
    }


    public function save(DomainObject $domainObject): bool
    {
        return $this->doSave($domainObject);
    }

    /**
     * @param DomainObject $domainObject
     * @return bool
     */
    public function update(DomainObject $domainObject): bool
    {
        return $this->doUpdate($domainObject);
    }

    /**
     * Insert new db record
     * @param DomainObject $obj
     * @return bool
     */
    protected function doSave(DomainObject $obj): bool
    {
        if ($obj->getId()) {
            return $this->update($obj);
        }

        $valuesMap = $this->propertiesResolver->getValuesMap($obj);
        $fields = $valuesMap['fields'];
        $values = $valuesMap['values'];

        $stmt = self::$PDO->prepare('INSERT INTO ' . $this->getTable() . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($values)) . ')');
        if ($stmt->execute($values)) {
            $obj->setId(self::$PDO->lastInsertId());
            return true;
        }
        return false;
    }

    /**
     * @param DomainObject $obj
     * @return bool
     */
    protected function doUpdate(DomainObject $obj): bool
    {
        $valuesMap = $this->propertiesResolver->getValuesMap($obj);
        $fields = $valuesMap['fields'];
        $values = $valuesMap['values'];

        $fieldsSql = [];

        foreach ($fields as $i => $field) {
            if($field != 'id') {
                $fieldsSql[] = $field . ' = :' . $field;
            }
        }

        $stmt = self::$PDO->prepare(
            'UPDATE ' . $this->getTable() . ' SET ' .
            implode(', ', $fieldsSql) .
            ' WHERE id = :id'
        );

        return $stmt->execute($values);
    }

    public function delete(DomainObject $object)
    {
        $stmt = self::$PDO->prepare('DELETE FROM ' . $this->getTable() . ' WHERE id = :id');
        $stmt->execute([':id' => $object->getId()]);
    }

    public function getFieldsList()
    {
        return $this->propertiesResolver->getFieldsList($this->getProperties());
    }

    /**
     * @return PDOStatement
     */
    private function findStmt()
    {
        if (!isset($this->findStmt)) {
            $this->findStmt = $this->getFindStmt();
        }
        return $this->findStmt;
    }

    /**
     * @return PDOStatement
     */
    public function findAllStmt()
    {
        if (!isset($this->findAllStmt)) {
            $this->findAllStmt = $this->getFindAllStmt();
        }
        return $this->findAllStmt;
    }

    /**
     * @return PDOStatement
     */
    protected function getFindStmt()
    {
        return self::$PDO->prepare('SELECT * FROM ' . $this->getTable() . ' WHERE id = :id');
    }

    /**
     * @return PDOStatement
     */
    protected function getFindALLStmt()
    {
        return self::$PDO->prepare('SELECT * FROM ' . $this->getTable());
    }

    /**
     * @return array
     */
    protected function getColumns(): array
    {
        $columns = array_map(function ($val) {
            return $val['field_name'];
        }, $this->getProperties()['columns']);
        return $columns;
    }


    /**
     * @return array
     */
    public abstract function getProperties(): array;

    /**
     * @return string
     */
    public abstract function getTable(): string;

    /**
     * @return string Entity classname
     */
    public abstract function targetEntity();

    /**
     * @param array $raw
     * @return Collection
     */
    public abstract function getCollection(array $raw);

}
