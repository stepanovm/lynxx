<?php


namespace app\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Lynxx\AbstractController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends AbstractController
{
    /** @var ServerRequestInterface  */
    private $request;

    /**
     * HomeController constructor.
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function home()
    {
        return new TextResponse('Hello, Maks!');
    }

    public function test()
    {
        $id = $this->request->getAttribute('id');
        $name = $this->request->getAttribute('name');
        return new JsonResponse(['action_response' => $id, 'name' => $name]);
    }
}