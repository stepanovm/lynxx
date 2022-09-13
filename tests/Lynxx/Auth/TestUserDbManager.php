<?php

namespace tests\Lynxx\Auth;

use Lynxx\Auth\UserInterface;

class TestUserDbManager implements \Lynxx\Auth\UserDbManagerInterface
{
    /**
     * этот пароль закодирован и используется в тестовом классе в качестве значения поля "пароль"
     * this pass is Encrypted to user value
     * @var string
     */
    public string $userPass = 'test_pass';


    public function getUserBySession(string $session): ?UserInterface
    {
        return new TestUser(2, '$2y$10$v18McO9kI5nz0c6jEAP43uuWyKjgo1AGwia94/Eej6CZ0JGqRzjDq', 99, 'test@email.ru');
    }

    public function getUserByLogin(string $login): ?UserInterface
    {
        if($login === 'login') {
            return new TestUser(2, '$2y$10$v18McO9kI5nz0c6jEAP43uuWyKjgo1AGwia94/Eej6CZ0JGqRzjDq', 99, 'test@email.ru');
        } else if ($login === 'zero_status') {
            return new TestUser(2, '$2y$10$v18McO9kI5nz0c6jEAP43uuWyKjgo1AGwia94/Eej6CZ0JGqRzjDq', 0, 'test@email.ru');
        }
        return null;
    }

    public function getUserById(int $id): ?UserInterface
    {
        return new TestUser(2, '$2y$10$v18McO9kI5nz0c6jEAP43uuWyKjgo1AGwia94/Eej6CZ0JGqRzjDq', 99, 'test@email.ru');
    }

    public function updateUserPassword(UserInterface $user, string $password): bool
    {
        $user->setPassword($password);
        return true;
    }

    public function updateSessionBy_id_agent_ip($sessId, $userId, $userAgent, $iserIp): bool
    {
        return true;
    }

    public function insertUserSession(UserInterface $user, string $sessId, string $userAgent, string $iserIp): bool
    {
        return true;
    }

    public function deleteSession(UserInterface $user, $session): bool
    {
        return true;
    }
}