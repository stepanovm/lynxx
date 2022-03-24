<?php


namespace tests\app\Controller;


use Laminas\Diactoros\Request;
use Laminas\Diactoros\ServerRequest;
use Lynxx\Container\Container;
use Lynxx\View;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HomeControllerTest extends TestCase
{
    public function testResponseOk()
    {
        $request = new ServerRequest();
        $view = new View(new Container());

        $controller = new \app\Controller\HomeController($view, $request);

        $response = $controller->test();

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals('Hello, Maks!', $response->getBody());
    }
}