<?php

namespace app\model\core;

/**
 * Class ObjectWatcher
 * @package app\model\domain
 */
class ObjectWatcher {

    /**
     * @var array
     */
    private $objects_map = [];

    /**
     * @param $class
     * @param $id
     * @return DomainObject|null
     */
    public function find($class, $id): ?DomainObject
    {
        $key = $class . '.' . $id;
        if(isset($this->objects_map[$key])){
            return $this->objects_map[$key];
        }
        return null;
    }
    
    /**
     * @param DomainObject $obj
     * @return string
     */
    public function globalKey(DomainObject $obj)
    {
        return get_class($obj) . '.' . $obj->getId();
    }
    
    /**
     * @param DomainObject $obj
     */
    public function add(DomainObject $obj)
    {
        $this->objects_map[$this->globalKey($obj)] = $obj;
    }

    public function getObjectsCount() {
        return count($this->objects_map);
    }
    
}
