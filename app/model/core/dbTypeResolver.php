<?php

namespace app\model\core;

use Psr\Container\ContainerInterface;

class dbTypeResolver
{

    private array $typesMap;

    /**
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->typesMap = $container->get('typesMap');
    }


    /**
     * @param string $type type - defined at "/app/config/dbTypes/types_map.php"
     * @param mixed $value
     * @return mixed
     */
    public function resolveToPhp(string $type, $value)
    {
        $typeClass = $this->getTypeClass($type);
        return $typeClass->toPhpValue($value);
    }


    /**
     * @param string $type type - defined at "/app/config/dbTypes/types_map.php"
     * @param $value
     * @return mixed
     */
    public function resolveToDatabase(string $type, $value)
    {
        $typeClass = $this->getTypeClass($type);
        return $typeClass->toDataBaseValue($value);
    }


    /**
     * @param string $type
     * @return dbTypeInterface
     */
    private function getTypeClass(string $type): dbTypeInterface
    {
        if(!array_key_exists($type, $this->typesMap)) {
            throw new \DomainException('Type ' . $type. ' is not defined at /app/config/dbTypes/types_map.php');
        }

        /** @var dbTypeInterface $typeClass */
        return new $this->typesMap[$type];
    }
}