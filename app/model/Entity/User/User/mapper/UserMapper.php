<?php

namespace app\model\Entity\User\User\mapper;

use app\model\core\Collection;
use app\model\Entity\User\User\User;
use app\model\Entity\User\UserSession\mapper\UserSessionMapper;
use app\model\Entity\User\UserSession\UserSession;
use Lynxx\Auth\UserDbManagerInterface;
use Lynxx\Auth\UserInterface;

class UserMapper extends \app\model\core\Mapper implements UserDbManagerInterface
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
                'status' => ['field_name' => 'status',],
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

    public function getUserBySession(string $session): ?UserInterface
    {

        $userSession = UserSession::getMapper()->findOneBy('session', $session);
        if (!is_null($userSession)) {
            /** @var UserSession $userSession */
            return $userSession->getUser();
        }
        return null;
    }

    public function getUserByLogin(string $login): ?UserInterface
    {
        $user = $this->findOneBy('login', $login);
        if ($user instanceof UserInterface) {
            return $user;
        }
        return null;
    }

    public function getUserById(int $id): ?UserInterface
    {
        $user = $this->find($id);
        if ($user instanceof UserInterface) {
            return $user;
        }
        return null;
    }

    public function updateUserPassword(UserInterface $user, string $password): bool
    {
        /** @var User $user */
        $user->setPassword($password);
        return $user->mapper()->save($user);
    }

    public function updateSessionBy_id_agent_ip($sessId, $userId, $userAgent, $userIp): bool
    {
        /** @var UserSessionMapper $sMapper */
        $sMapper = UserSession::getMapper();
        return $sMapper->updateSessionBy_id_agent_ip($sessId, $userId, $userAgent, $userIp);
    }

    public function insertUserSession(UserInterface $user, string $sessId, string $userAgent, string $userIp): bool
    {
        $session = new UserSession(
            $sessId,
            $userIp,
            $userAgent,
            new \DateTimeImmutable(date('Y-m-d H:i:s')),
            $user
        );

        /** @var User $user */
        $user->getUserSessions()->add($session);
        return $session->mapper()->save($session);
    }

    public function deleteSession(UserInterface $user, $session): bool
    {
        /** @var UserSessionMapper $sMapper */
        $sMapper = UserSession::getMapper();
        if ($user instanceof User) {
            return $sMapper->deleteSession($user, $session);
        }
        return false;
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