<?php

namespace WishgranterProject\Backend\Authentication;

class User implements AuthenticationMethodInterface
{
    protected $userName;

    protected $hash;

    public function __construct()
    {

    }

    public function validatePassword(string $password): bool
    {
        $hash = $this->getHash($password);
        return $hash == $this->hash;
    }

    public function getHash(string $string): string
    {
        return md5($string);
    }
}
