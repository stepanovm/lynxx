<?php

namespace app\model\Entity\User\UserSession\mapper;

use app\model\core\Collection;
use app\model\Entity\User\User\User;
use app\model\Entity\User\UserSession\UserSession;

class UserSessionMapper extends \app\model\core\Mapper
{

    /**
     * @inheritDoc
     */
    public function getProperties(): array
    {
        return [
            'columns' => [
                'id' => ['field_name' => 'id',],
                'session' => ['field_name' => 'session',],
                'userIp' => ['field_name' => 'userIP',],
                'userAgent' => ['field_name' => 'userAgent',],
                'date' => ['field_name' => 'date_create', 'type' => 'dateTime'],
                'user' => [
                    'field_name' => 'user_id',
                    'targetEntity' => User::class
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        return 'user_session';
    }

    /**
     * @inheritDoc
     */
    public function targetEntity()
    {
        return UserSession::class;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(array $raw): Collection
    {
        return new UserSessionCollection($raw, $this);
    }
}