<?php


namespace tests\app\Controller;


use Laminas\Diactoros\Request;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HomeControllerTest extends TestCase
{
    public function testResponseOk()
    {
        $request = new ServerRequest();

        $controller = new \app\Controller\HomeController($request);

        $response = $controller->home();

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals('Hello, Maks!', $response->getBody());
    }
}