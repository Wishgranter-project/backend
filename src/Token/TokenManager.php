<?php

namespace WishgranterProject\Backend\Token;

class TokenManager
{
    public function validateToken(TokenInterface $token, string $action): bool
    {
        return !$token->isExpired() && $token->getAction() == $action;
    }

    public function expendToken(TokenInterface $token, string $action): bool
    {
        if (!$this->validateToken($token, $action)) {
            return false;
        }

        $token->delete();

        return true;
    }
}
