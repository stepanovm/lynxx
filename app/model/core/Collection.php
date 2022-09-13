<?php

namespace app\model\core;

abstract class Collection implements \Iterator, \ArrayAccess, \Countable
{

    protected $total = 0;
    private $pointer = 0;

    protected $mapper;
    protected $raw = [];
    protected $objects = [];

    /**
     * Collection constructor.
     * @param array|null $raw
     * @param Mapper|null $mapper
     */
    public function __construct(array $raw = null, Mapper $mapper = null)
    {
        if (!is_null($raw) && !is_null($mapper)) {
            $this->raw = $raw;
            $this->total = count($raw);
        }
        $this->mapper = $mapper;
    }

    /**
     * @param DomainObject $obj
     * @throws \Exception
     */
    public function add(DomainObject $obj)
    {
        $class = $this->targetClass();
        if (!($obj instanceof $class)) {
            throw new \Exception("bad obj type, this is collection of {$class}");
        }
        $this->notifyAccess();
        $this->objects[$this->total] = $obj;
        $this->total++;
    }

    /**
     * @param int $num
     * @return DomainObject|bool|mixed|null
     */
    public function getRow($num)
    {
        $this->notifyAccess();

        if ($num >= $this->total || $num < 0) {
            return null;
        }

        if (array_key_exists($num, $this->objects)) {
            return $this->objects[$num];
        }

        if (array_key_exists($num, $this->raw)) {
            $obj = $this->mapper->createObject($this->raw[$num]);
            $this->objects[$num] = $obj;
            return $this->objects[$num];
        }
    }

    /**
     * empty for lazy load
     */
    protected function notifyAccess()
    {
    }

    /**
     * @return string DomainObject classname
     */
    abstract function targetClass();


    public function current()
    {
        return $this->getRow($this->pointer);
    }

    public function key()
    {
        return $this->pointer;
    }

    public function next()
    {
        $row = $this->getRow($this->pointer);
        if ($row) {
            $this->pointer++;
        }
        return $row;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function valid()
    {
        return (!is_null($this->current()));
    }


    public function offsetExists($offset)
    {
        return boolval($offset < $this->total && $offset >= 0);
    }

    public function offsetGet($offset)
    {
        return $this->getRow($offset);
    }

    public function offsetSet($offset, $obj)
    {
        $this->add($obj);
    }

    public function offsetUnset($offset)
    {
        unset($this->raw[$offset]);
        unset($this->objects[$offset]);
        $this->raw = array_values($this->raw);
        $this->objects = array_values($this->objects);
        $this->total--;
    }

    public function count()
    {
        return count($this->raw);
    }


}


