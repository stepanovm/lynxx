<?php


namespace app\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Lynxx\AbstractController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends AbstractController
{
    public function home(RequestInterface $request)
    {
        return new TextResponse('Hello, Maks!');
    }

    public function test(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $name = $request->getAttribute('name');
        return new JsonResponse(['action_response' => $id, 'name' => $name]);
    }
}