<?php

namespace app\Controller;

use Laminas\Diactoros\Response\TextResponse;
use Lynxx\AbstractController;
use Lynxx\View;
use Psr\Http\Message\ServerRequestInterface;


class HomeController extends AbstractController
{
    private View $view;

    public function __construct(View $view, ServerRequestInterface $request)
    {
        $this->view = $view;
    }

    public function home()
    {
        return $this->view->render('home.php', [
            'name' => 'Guest'
        ]);
    }

    public function test()
    {
        return new TextResponse('Hello, Maks!');
    }
}