<?php

namespace WishgranterProject\Backend\Token;

use WishgranterProject\Backend\UserGrant\UserGrantInterface;

interface TokenInterface extends UserGrantInterface
{
    /**
     * Return the action the token clears.
     *
     * @return string
     *   The action.
     */
    public function getAction(): string;
}
