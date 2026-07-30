<?php

namespace app\Controller\request;

use Laminas\Diactoros\Response\JsonResponse;
use Lynxx\AbstractController;
use Lynxx\Lynxx;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends AbstractController
{
    private RequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function authByPass(): JsonResponse
    {
        try {
            $requestData = $this->request->getParsedBody();

            if(Lynxx::Auth()->authByPassword($requestData['login'], $requestData['pass'])) {
                return new JsonResponse([]);
            }
            throw new \Exception(Lynxx::Auth()->getLastError());

        } catch (\Throwable $ex) {
            return new JsonResponse(['error' => $ex->getMessage()]);
        }
    }
}