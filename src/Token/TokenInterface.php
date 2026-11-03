<?php

namespace WishgranterProject\Backend\Token;

use WishgranterProject\Backend\ExpirableCredential\ExpirableCredentialInterface;

interface TokenInterface extends ExpirableCredentialInterface
{
    /**
     * Return the action the token clears.
     *
     * @return string
     *   The action.
     */
    public function getAction(): string;
}
