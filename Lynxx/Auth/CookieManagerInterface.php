<?php

namespace Lynxx\Auth;

interface CookieManagerInterface
{
    /**
     * @param string $name
     * @param string $value
     * @param int $time
     * @return void
     */
    public function set(string $name, string $value, int $time);

    /**
     * @param string $string
     * @return string|null
     */
    public function get(string $string): ?string;

    public function clear(string $name);
}