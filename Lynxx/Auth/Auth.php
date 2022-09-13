<?php

namespace Lynxx\Auth;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of Auth
 *
 * @author StepanovM
 */
class Auth
{

    private ?string $lastError;
    private ServerRequestInterface $request;
    private UserDbManagerInterface $userDbManager;
    private CookieManagerInterface $cookieManager;
    private ContainerInterface $container;


    /**
     * @param UserDbManagerInterface $userDbManager
     * @param ServerRequestInterface $request
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        UserDbManagerInterface $userDbManager,
        ServerRequestInterface $request,
        CookieManagerInterface $cookieManager,
        ContainerInterface $container
    )
    {
        $this->lastError = null;
        $this->userDbManager = $userDbManager;
        $this->request = $request;
        $this->cookieManager = $cookieManager;
        $this->container = $container;
    }



    /**
     * check auth in SESSION. if false check cookie
     * @return boolean
     */
    public function isLoggedIn()
    {
        if (isset($_SESSION['auth'])) {
            return true;
        } else {
            return $this->authByCookie();
        }
    }



    /**
     * @return boolean
     */
    private function authByCookie()
    {
        $cookieAuth = $this->cookieManager->get('auth');
        if ($cookieAuth) {
            $user = $this->userDbManager->getUserBySession($cookieAuth);
            if (!is_null($user)) {
                $this->addSessionUserData($user);
                return true;
            }
        }
        return false;
    }



    /**
     * @param string $incLogin
     * @param string $incPass
     * @return bool
     */
    public function authByPassword(string $incLogin = '', string $incPass = '')
    {
        $isAuth = false;
        if (!empty($incLogin) && !empty($incPass)) {
            $login = trim($incLogin);
            $pass = trim($incPass);
            $user = $this->userDbManager->getUserByLogin($login);
            if (!is_null($user)) {
                if (self::passVerify($pass, $user->getPassword())) {
                    if ($user->getStatus() > UserInterface::STATUS_GUEST) {
                        $this->addSessionUserData($user);
                        $this->setNewAuthCookie($user);
                        $isAuth = true;
                    } else {
                        $this->setLastError("Пользователь " . $login . " не авторизован. Пожалуйста, завершите регистрацию, пройдя"
                            . " по ссылке в письме на " . $user->getEmail());
                    }
                } else {
                    $this->setLastError('неверный пароль');
                }
            } else {
                $this->setLastError('пользователь ' . $login . ' не найден');
            }
        } else {
            $this->setLastError('не введены данные');
        }
        return $isAuth;
    }



    /**
     * @param UserInterface $user
     * @return void
     */
    private function addSessionUserData(UserInterface $user)
    {
        $_SESSION['auth'] = true;
        $_SESSION['user'] = [
            'id' => $user->getId(),
            //'status' => $user->getStatus(),
            //'password' => $user->getPassword(),
            //'email' => $user->getEmail(),
        ];


    }



    /**
     * save user cookies and save data to DB
     * @param UserInterface $user
     * @return void
     */
    private function setNewAuthCookie(UserInterface $user)
    {
        $sessId = self::createUniqId();

        $this->cookieManager->set('auth', $sessId, $this->container->get('config')['authCookieTime']);

        $userAgent = ($this->request->getServerParams())['HTTP_USER_AGENT'];
        $userIp = ($this->request->getServerParams())['REMOTE_ADDR'];

        // try to check device existance and update user_sessions:
        if (!$this->userDbManager->updateSessionBy_id_agent_ip($sessId, $user->getId(), $userAgent, $userIp)) {
            // if unsuccess — new device, insert:
            $this->userDbManager->insertUserSession($user, $sessId, $userAgent, $userIp);
        }
    }



    /**
     * @return void
     */
    public function logout()
    {
        $currentUser = $this->userDbManager->getUserById($_SESSION['user']['id']);
        if ($currentUser instanceof UserInterface) {
            // remove session in the database
            $this->userDbManager->deleteSession($currentUser, $this->cookieManager->get('auth'));
        }

        // rewrite cookie:
        $this->cookieManager->clear('auth');
        $this->cookieManager->clear('PHPSESSID');

        //unset($_COOKIE[session_name()]);

        // destroy session:
        $_SESSION = [];
        if(session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }



    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    private function setLastError(?string $lastError): void
    {
        $this->lastError = $lastError;
    }



    // return encrypted hash
    public static function passEncrypt($str)
    {
        return password_hash($str, PASSWORD_DEFAULT);
    }

    // return true if
    public static function passVerify($str, $hash)
    {
        return password_verify($str, $hash);
    }

    // return uniq string id (can use in cookie session as example)
    public static function createUniqId()
    {
        return self::passEncrypt(uniqid(rand(),1));
    }

}