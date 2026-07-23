<?php


namespace app\model\core;


class DomainObjectFactory
{
    /** @var ObjectWatcher */
    private $objWatcher;
    /** @var Hydrator  */
    private $hydrator;

    /**
     * DomainObjectFactory constructor.
     * @param ObjectWatcher $objWatcher
     * @param Hydrator $hydrator
     */
    public function __construct(ObjectWatcher $objWatcher, Hydrator $hydrator)
    {
        $this->hydrator = $hydrator;
        $this->objWatcher = $objWatcher;
    }

    public function createObject(string $entityClassName, array $propertiesMap, array $array)
    {
        $old = $this->getFromMap($entityClassName, $array['id']);
        if ($old) {
            return $old;
        }
        $obj = $this->hydrator->hydrate($entityClassName, $propertiesMap, $array);
        $this->addToMap($obj);
        return $obj;
    }

    /**
     * @param DomainObject $obj
     */
    private function addToMap(DomainObject $obj): void
    {
        $this->objWatcher->add($obj);
    }

    /**
     * @param string $entityClassName
     * @param int $id
     * @return DomainObject|null
     */
    public function getFromMap(string $entityClassName, int $id): ?DomainObject
    {
        return $this->objWatcher->find($entityClassName, $id);
    }


}