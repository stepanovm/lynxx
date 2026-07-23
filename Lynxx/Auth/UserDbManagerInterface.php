<?php

namespace Lynxx\Auth;

interface UserDbManagerInterface
{
    public function getUserBySession(string $session): ?UserInterface;
    public function getUserByLogin(string $login): ?UserInterface;
    public function getUserById(int $id): ?UserInterface;
    public function updateUserPassword(UserInterface $user, string $password): bool;
    public function updateSessionBy_id_agent_ip($sessId, $userId, $userAgent, $iserIp): bool;
    public function insertUserSession(UserInterface $user, string $sessId, string $userAgent, string $iserIp): bool;
    public function deleteSession(UserInterface $user, $session): bool;
}