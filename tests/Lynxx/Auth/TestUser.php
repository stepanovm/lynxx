<?php

namespace tests\Lynxx\Auth;

class TestUser implements \Lynxx\Auth\UserInterface
{

    private int $id;
    private string $password;
    private int $status;
    private string $email;

    /**
     * @param int $id
     * @param string $password
     * @param int $status
     * @param string $email
     */
    public function __construct(int $id, string $password, int $status, string $email)
    {
        $this->id = $id;
        $this->password = $password;
        $this->status = $status;
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }




}