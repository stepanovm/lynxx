<?php

namespace app\model\Entity\User\UserSession\mapper;

use app\model\core\Collection;
use app\model\Entity\User\UserSession\UserSession;

class UserSessionCollection extends Collection
{
    /**
     * @return string
     */
    function targetClass(): string
    {
        return UserSession::class;
    }

}