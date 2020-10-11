<?php


namespace tests\app\Controller;


use Laminas\Diactoros\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HomeControllerTest extends TestCase
{
    public function testResponseOk()
    {
        $request = new Request();

        $controller = new \app\Controller\HomeController();

        $response = $controller->home($request);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals('Hello, Maks!', $response->getBody());
    }
}