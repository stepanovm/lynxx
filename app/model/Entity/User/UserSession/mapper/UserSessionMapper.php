<?php

namespace app\model\Entity\User\UserSession\mapper;

use app\model\core\Collection;
use app\model\Entity\User\User\User;
use app\model\Entity\User\UserSession\UserSession;
use Lynxx\Auth\UserInterface;

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

    public function updateSessionBy_id_agent_ip($sessId, $userId, $userAgent, $userIp): bool
    {
        $query = "UPDATE user_session SET session = :sess_id WHERE user_id = :user_id AND userAgent = :userAgent AND userIP = :user_ip";

        $stmt = self::$PDO->prepare($query);
        $stmt->execute([
            'sess_id' => $sessId,
            'user_id' => $userId,
            'userAgent' => $userAgent,
            'user_ip' => $userIp,
        ]);
        if ($stmt->rowCount() >= 1) {
            return true;
        }
        return false;
    }


    public function deleteSession(User $user, $session): bool
    {
        $stmt = self::$PDO->prepare("DELETE FROM user_session WHERE user_id = :user_id AND session = :session");
        return $stmt->execute(['user_id' => $user->getId(), 'session' => $session]);
    }
}