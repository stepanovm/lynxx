<?php

namespace Lynxx\Auth;

interface UserInterface
{
    const STATUS_GUEST = 0;

    public function getId(): ?int;
    public function getPassword(): ?string;
    public function getStatus(): int;
    public function getEmail(): ?string;
    public function setPassword(string $password);
}