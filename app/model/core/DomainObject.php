<?php

namespace app\model\core;


use Lynxx\Container\Container;
use Lynxx\Lynxx;
use Psr\Container\ContainerInterface;

abstract class DomainObject
{

    private static ContainerInterface $container;

    /**
     * @var int object id
     */
    protected $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Mapper
     */
    public function mapper()
    {
        return self::getMapper(get_class($this));
    }

    /**
     * @param null $domObjClassName
     * @return Mapper
     */
    public static function getMapper($domObjClassName = null): Mapper
    {
        if (!isset(self::$container)) {
            try {
                self::$container = Lynxx::getContainer();
            } catch (\Throwable $ex) {
                self::$container = new Container();
            }
        }
        $mapper = self::$container->get(self::$container->get('mappersMap')[$domObjClassName ?? get_called_class()]);

        return $mapper;
    }

    public function toArray()
    {
        $result = [];
        $refl = new \ReflectionClass($this);
        foreach ($refl->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC) as $property) {
            $result[$property->name] = is_object($this->{$property->name}) ? 'object' : $this->{$property->name};
        }
        return $result;
    }

    public function __get($property)
    {
        return 'property "' . $property . '" is undefined or private access';
    }

}
