<?php

namespace app\model\Entity\User\UserSession;

use app\model\core\DomainObject;
use app\model\Entity\User\User\User;
use Lynxx\Auth\UserInterface;

class UserSession extends DomainObject
{
    private string $session;
    private string $userIp;
    private string $userAgent;
    private \DateTimeImmutable $date;
    private User $user;

    /**
     * @param string $session
     * @param string $userIp
     * @param string $userAgent
     * @param \DateTimeImmutable $date
     * @param UserInterface $user
     */
    public function __construct(string $session, string $userIp, string $userAgent, \DateTimeImmutable $date, UserInterface $user)
    {
        $this->session = $session;
        $this->userIp = $userIp;
        $this->userAgent = $userAgent;
        $this->date = $date;
        $this->user = $user;
    }

    public function getSession(): string
    {
        return $this->session;
    }

    public function getUserIp(): string
    {
        return $this->userIp;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }




}