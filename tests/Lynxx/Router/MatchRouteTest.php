<?php


namespace tests\Lynxx\Router;


use app\Controller\HomeController;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Lynxx\Router\Route;
use Lynxx\Router\RouteNotFoundException;
use Lynxx\Router\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class MatchRouteTest extends TestCase
{
    public function testRouteMatch()
    {
        $request = (new ServerRequest())
            ->withUri(new Uri('http://lynxx.loc/test/Maks/44'));

        $router = new Router($request, [
            '/test/(?<name>\w+)/(?<id>\d+)' => [HomeController::class, 'test'],
        ]);

        self::assertNotEmpty($router->getRoutesMap());
        self::assertInstanceOf(Route::class, $router->getRoute());
        self::assertInstanceOf(ServerRequestInterface::class, $router->getRequest());

        self::assertEquals(HomeController::class, $router->getController());
        self::assertEquals('test', $router->getAction());
        self::assertEquals('Maks', $router->getAttributes()['name']);
        self::assertEquals(44, $router->getAttributes()['id']);

    }

    public function testNotFound()
    {
        $request = (new ServerRequest());

        self::expectExceptionObject(new RouteNotFoundException('route not found'));

        $router = new Router($request, []);
    }
}