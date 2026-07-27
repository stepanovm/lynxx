<?php

namespace app\model\Entity\User\User\mapper;

use app\model\core\Collection;
use app\model\Entity\User\User\User;
use app\model\Entity\User\UserSession\UserSession;

class UserMapper extends \app\model\core\Mapper
{

    /**
     * @inheritDoc
     */
    public function getProperties(): array
    {
        return [
            'columns' => [
                'id' => ['field_name' => 'id',],
                'login' => ['field_name' => 'login',],
                'email' => ['field_name' => 'email',],
                'password' => ['field_name' => 'password',],
                'regDate' => ['field_name' => 'reg_date', 'type' => 'dateTime'],
            ],
            'relations' => [
                'userSessions' => [
                    'targetEntity' => UserSession::class,
                    'byColumn' => 'user_id',
                    'mapperMethod' => 'findManyBy',
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        return 'user';
    }

    /**
     * @inheritDoc
     */
    public function targetEntity()
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(array $raw): Collection
    {
        return new UserCollection($raw, $this);
    }
}