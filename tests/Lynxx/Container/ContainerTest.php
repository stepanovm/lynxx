<?php

/**
 * php vendor/phpunit/phpunit/phpunit tests --bootstrap ./Lynxx/autoload.php
 */

namespace tests\Lynxx\Container;

use Lynxx\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testPrimitives()
    {
        $container = new Container();

        $container->set($name = 'name', $value = 5);
        self::assertEquals($value, $container->get($name));
        $container->set($name = 'string', $value = 'string');
        self::assertEquals($value, $container->get($name));
        $container->set($name = 'array', $value = ['array']);
        self::assertEquals($value, $container->get($name));
        $container->set($name = 'stdClass', $value = new \stdClass());
        self::assertEquals($value, $container->get($name));
    }

    public function testExist()
    {
        $container = new Container();
        $id = 'existing test';
        self::expectExceptionMessage('service ' . $id . ' already exist');

        $container->set($id, 5);
        $container->set($id, 6);
    }

    public function testCallback()
    {
        $container = new Container();
        $id = 1;

        $container->set($id, function () {
            return new \stdClass();
        });

        self::assertNotNull($container->get($id));
        self::assertInstanceOf(\stdClass::class, $container->get($id));
    }

    public function testNotFound()
    {
        $container = new Container();
        $id = 'badId';

        self::expectExceptionMessage('service ' . $id . ' not found');

        $container->get($id);
    }

    public function testCreateObjWithNoServiceRegistered()
    {
        $container = new Container();

        self::assertNotNull($obj = $container->get(NoRegisteredAtContainerClass::class));
        self::assertInstanceOf(NoRegisteredAtContainerClass::class, $obj);
    }
}

class NoRegisteredAtContainerClass
{

}