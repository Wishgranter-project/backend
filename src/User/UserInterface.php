<?php

namespace WishgranterProject\Backend\User;

interface UserInterface
{
    /**
     * Return the user's username.
     *
     * @return string
     *   Username.
     */
    public function getUsername(): ?string;

    /**
     * Return the hash for the user's password.
     *
     * @return string
     *   Hash.
     */
    public function getHash(): string;
}
