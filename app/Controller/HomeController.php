<?php


namespace app\Controller;


use Lynxx\AbstractController;
use Lynxx\Utils;

class HomeController extends AbstractController
{
    public function home()
    {
        echo 'Hello, World! =)';

        echo Utils::debugObj($_SERVER);
    }
}