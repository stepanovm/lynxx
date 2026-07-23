<?php

namespace Lynxx\Auth;

class CookieManager implements CookieManagerInterface
{
    /**
     * @param string $name
     * @param string $value
     * @param int $time
     * @return void
     */
    public function set(string $name, string $value, int $time = 0)
    {
        setcookie($name, $value, $time, '/');
    }

    /**
     * @param string $string
     * @return string|null
     */
    public function get(string $string): ?string
    {
        $cookieAuth = filter_input(INPUT_COOKIE, 'auth', FILTER_SANITIZE_STRING);
        if (!empty($cookieAuth)) {
            return $cookieAuth;
        }
        return false;
    }

    public function clear(string $name)
    {
        setcookie($name, '', time(), '/');
    }
}