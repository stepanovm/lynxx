<?php

namespace tests\Lynxx\Auth;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Lynxx\Auth\Auth;
use Lynxx\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class AuthTest extends \PHPUnit\Framework\TestCase
{
    private Container $container;
    private Auth $auth;


    protected function setUp(): void
    {
        $this->container = new Container();
        /** @var ServerRequestInterface $request */
        $request = (new ServerRequest())->withUri(new Uri('http://lynxx.loc/'));

        $this->auth = new Auth(
            new TestUserDbManager(),
            $request,
            new TestCookieManager(),
            $this->container
        );
    }


    public function testAuthByPass_emptyData()
    {
        $this->auth->authByPassword('', '');
        self::assertEquals('не введены данные', $this->auth->getLastError());
        $this->auth->authByPassword('111111', '');
        self::assertEquals('не введены данные', $this->auth->getLastError());
        $this->auth->authByPassword('', '1111111');
        self::assertEquals('не введены данные', $this->auth->getLastError());
    }


    public function testAuthByPass_wrongData()
    {
        $badLogin = 'badlogin';
        $this->auth->authByPassword($badLogin, 'pass');
        self::assertEquals('пользователь '.$badLogin.' не найден', $this->auth->getLastError());

        $badPass = 'badPass';
        $this->auth->authByPassword('login', $badPass);
        self::assertEquals('неверный пароль', $this->auth->getLastError());

        $this->auth->authByPassword('zero_status', 'test_pass');
        self::assertEquals("Пользователь zero_status не авторизован. Пожалуйста, завершите регистрацию, пройдя"
            . " по ссылке в письме на test@email.ru", $this->auth->getLastError());
    }

    public function testSuccessLogin()
    {
        self::assertTrue($this->auth->authByPassword('login', 'test_pass'));
        self::assertEquals($_SESSION['user']['id'], 2);
    }

    public function testLogout()
    {
        $this->auth->logout();

        self::assertEquals(false, $this->auth->isLoggedIn());
    }

}