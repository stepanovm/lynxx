<?php


namespace Lynxx\Container;


use http\Exception\InvalidArgumentException;

class Container
{
    private $singltones = [];
    private $definitions = [];

    public function get($id)
    {
        if(array_key_exists($id, $this->singltones)) {
            return $this->singltones[$id];
        }

        if (!array_key_exists($id, $this->definitions)) {
            throw new ServiceNotFoundException('service ' . $id . ' not found');
        }

        $definition = $this->definitions[$id];
        if ($definition instanceof \Closure) {
            $this->singltones[$id] = $definition();
        } else {
            $this->singltones[$id] = $definition;
        }

        return $this->singltones[$id];
    }

    public function set($id, $value)
    {
        if(array_key_exists($id, $this->definitions)){
            throw new \InvalidArgumentException('service ' . $id . ' already exist');
        }
        $this->definitions[$id] = $value;
    }
}