<?php

namespace WishgranterProject\Backend\Token;

use WishgranterProject\Backend\User\UserInterface;

interface TokenManagerInterface
{
    /**
     * Generates a new token.
     *
     * @param WishgranterProject\Backend\User\UserInterface $user
     *   The user issuing the token.
     * @param string
     *   The action the token clears.
     * @param int $expiration
     *   When the session is set to expire.
     *
     * @return WishgranterProject\Backend\Token\TokenInterface
     *   The newly minted token.
     */
    public function issueNewToken(UserInterface $user, string $action, int $expiration): TokenInterface;

    /**
     * Given and Id, retrieves the respective token.
     *
     * @param string $tokenId
     *   The token id.
     *
     * @return null|WishgranterProject\Backend\Token\TokenInterface
     *   The token.
     */
    public function getToken(string $tokenId): ?TokenInterface;

    /**
     * Return all tokens to iterate through them.
     */
    public function getTokens();

    /**
     * Checks if the token exists.
     *
     * @param string $tokenId
     *   The token id.
     *
     * @return bool
     *   If the token exists or not.
     */
    public function tokenExists(string $tokenId): bool;

    /**
     * Validates the token.
     *
     * @param WishgranterProject\Backend\Token\TokenInterface $token
     *   The token.
     *
     * @return bool
     *   If the token is valid.
     */
    public function validateToken(TokenInterface $token, string $action): bool;

    /**
     * Expends the token.
     *
     * @param WishgranterProject\Backend\Token\TokenInterface $token
     *   The token.
     *
     * @return bool
     *   If the token is valid.
     */
    public function expendToken(TokenInterface $token, string $action): bool;
}
