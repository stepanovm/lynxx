<?php

/**
 * php vendor/phpunit/phpunit/phpunit tests --bootstrap ./Lynxx/autoload.php
 */

namespace tests\Lynxx\Container;

use Lynxx\Container\Container;
use Lynxx\Container\ContainerException;
use Lynxx\Container\ServiceNotFoundException;
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

    public function testHas()
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

        $container->set($id = 1, function () {
            return new \stdClass();
        });

        self::assertNotNull($container->get($id));
        self::assertInstanceOf(\stdClass::class, $container->get($id));
    }

    public function testNotFound()
    {
        $container = new Container();
        $id = 'badId';
        self::expectException(ServiceNotFoundException::class);
        $container->get($id);
    }

    public function testGetClassNyName()
    {
        $container = new Container();
        $foo = $container->get(\stdClass::class);
        $bar = $container->get(\stdClass::class);

        self::assertInstanceOf(\stdClass::class, $bar);
        self::assertEquals($foo, $bar);
    }

    public function testClassWithConstructor()
    {
        $container = new Container();
        /** @var A $obj */
        $obj = $container->get(A::class);

        self::assertInstanceOf(B::class, $obj->b);
        self::assertEquals(15, $obj->int);
        self::assertIsArray($obj->array);
    }
/*
    public function testBadClassArgument()
    {
        $container = new Container();
        self::expectException(ContainerException::class);
        $container->get(C::class);
    }
*/

}


class B {}
class A
{
    public $b;
    public $array;
    public $int;

    public function __construct(B $b, array $array, int $int = 15)
    {
        $this->b = $b;
        $this->array = $array;
        $this->int = $int;
    }
}
class C {
    public $test;
    public function __construct(string $test)
    {
        $this->test = $test;
    }
}