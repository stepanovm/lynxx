<?php


namespace app\model\core;


use ReflectionException;

class Hydrator
{
    private $reflectionClassMap;

    private dbTypeResolver $dbTypesResolver;

    /**
     * @param dbTypeResolver $dbTypesResolver
     */
    public function __construct(dbTypeResolver $dbTypesResolver)
    {
        $this->dbTypesResolver = $dbTypesResolver;
    }


    /**
     * @param string $class
     * @param array $propertiesMap
     * @param array $rawData
     * @return DomainObject
     * @throws ReflectionException
     */
    public function hydrate(string $class, array $propertiesMap, array $rawData): DomainObject
    {
        $entity = $this->doHydrate($class, $propertiesMap, $rawData);
        if(!$entity instanceof DomainObject) {
            throw new \DomainException('bad entity type');
        }
        return $entity;
    }



    private function doHydrate(string $class, array $propertiesMap, array $rawData)
    {
        $reflection = $this->getReflectionClass($class);
        $target = $reflection->newInstanceWithoutConstructor();

        foreach ($propertiesMap['columns'] as $mapProperty => $params) {

            $property = $reflection->getProperty($mapProperty);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }

            if(array_key_exists('embeddedClass', $params)) {
                $property->setValue($target, $this->doHydrate($params['embeddedClass'], $params, $rawData));
                continue;
            }

            if (!isset($rawData[$params['field_name']])) {
                $property->setValue($target, null);
                continue;
            }

            if (isset($params['targetEntity'])) {
                /** @var DomainObject $targetEntityClass */
                $targetEntityClass = $params['targetEntity'];
                $targetMapper = $target::getMapper($targetEntityClass);

                //if (isset($rawData[$mapProperty])) {
                if (array_key_exists($mapProperty, $rawData)) {
                    $test = $rawData[$mapProperty];
                    if (isset($rawData[$mapProperty])) {
                        $property->setValue($target, $targetMapper->createObject($rawData[$mapProperty]));
                    } else {
                        $property->setValue($target, null);
                    }
                } else {
                    $property->setValue($target, $targetMapper->find($rawData[$params['field_name']]));
                }
            } else {
                if(array_key_exists('type', $params)){
                    $property->setValue($target, $this->dbTypesResolver->resolveToPhp($params['type'], $rawData[$params['field_name']]));
                } else {
                    $property->setValue($target, $rawData[$params['field_name']]);
                }
            }
        }



        if (isset($propertiesMap['relations'])) {
            foreach ($propertiesMap['relations'] as $mapProperty => $relation) {
                $property = $reflection->getProperty($mapProperty);
                if ($property->isPrivate() || $property->isProtected()) {
                    $property->setAccessible(true);
                }

                /** @var DomainObject $targetEntityClass */
                $targetEntityClass = $relation['targetEntity'];
                $targetMapper = $target::getMapper($targetEntityClass);

                /*
                 * If relation data already loaded (by JOIN for example)
                 * just create collection
                 */
                if (isset($rawData[$mapProperty])) {
                    $property->setValue($target, $targetMapper->getCollection($rawData[$mapProperty]));
                    continue;
                }

                /*
                 * otherwise just load data by mapper
                 */
                $targetMapperMethod = $relation['mapperMethod'];
                $args = [];
                $args[] = $target->getId();
                if (isset($relation['byColumn'])) {
                    $args[] = $relation['byColumn'];
                }
                $property->setValue($target, $targetMapper->$targetMapperMethod(...$args));
            }
        }


        return $target;

    }


    private function getReflectionClass($className)
    {
        if (!isset($this->reflectionClassMap[$className])) {
            $this->reflectionClassMap[$className] = new \ReflectionClass($className);
        }
        return $this->reflectionClassMap[$className];
    }

    public function reflectionsCount(): int
    {
        return count($this->reflectionClassMap);
    }
}