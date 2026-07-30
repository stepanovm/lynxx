<?php

namespace app\model\Entity\User\User;

use app\model\core\DomainObject;
use app\model\Entity\User\UserSession\mapper\UserSessionCollection;
use Lynxx\Auth\UserInterface;

class User extends DomainObject implements UserInterface
{

    private string $login;
    private string $password;
    private string $email;
    private int $status;
    private ?\DateTimeImmutable $regDate;
    private ?UserSessionCollection $userSessions;

    /**
     * @param string $login
     * @param string $password
     * @param string $email
     * @param string $status
     */
    public function __construct(string $login, string $password, string $email, int $status)
    {
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->status = $status;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getRegDate(): ?\DateTimeImmutable
    {
        return $this->regDate;
    }

    public function getUserSessions(): ?UserSessionCollection
    {
        return $this->userSessions;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}