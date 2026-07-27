<?php

namespace app\model\Entity\User\User\mapper;

use app\model\core\Collection;
use app\model\Entity\User\User\User;

class UserCollection extends Collection
{
    /**
     * @return string
     */
    function targetClass(): string
    {
        return User::class;
    }

}