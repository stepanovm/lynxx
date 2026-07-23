<?php

namespace tests\Lynxx\Auth;

use Lynxx\Auth\CookieManagerInterface;

class TestCookieManager implements CookieManagerInterface
{

    private $cookies = [];

    /**
     * @param string $name
     * @param string $value
     * @param int $time
     * @return void
     */
    public function set(string $name, string $value, int $time)
    {
        $this->cookies[$name] = [$value, $time];
    }

    /**
     * @param string $string
     * @return string|null
     */
    public function get(string $string): ?string
    {
        $cookieAuth = $this->cookies['$string'][0] ?? null;
        if (!is_null($cookieAuth)) {
            return $cookieAuth;
        }
        return false;
    }

    public function clear(string $name)
    {
        unset($this->cookies[$name]);
    }
}