<?php

namespace WishgranterProject\Backend\Token;

use WishgranterProject\Backend\UserGrant\UserGrantManager;
use WishgranterProject\Backend\UserGrant\UserGrantInterface;

class TokenManager extends UserGrantManager implements TokenManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getToken(string $tokenId): ?SessionInterface
    {
        return $this->getGrant($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function tokenExists(string $tokenId): bool
    {
        return $this->grantExists($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokens()
    {
        return $this->getGrants();
    }

    /**
     * {@inheritdoc}
     */
    public function validateToken(TokenInterface $token, string $action): bool
    {
        return !$token->isExpired() && $token->getAction() == $action;
    }

    /**
     * {@inheritdoc}
     */
    public function expendToken(TokenInterface $token, string $action): bool
    {
        if (!$this->validateToken($token, $action)) {
            return false;
        }

        $token->delete();

        return true;
    }
}
