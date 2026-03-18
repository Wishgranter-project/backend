<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserInterface;

interface SessionManagerInterface
{
    /**
     * Fetches a session object.
     *
     * @param string $sessionId
     *   The session id.
     *
     * @return null|WishgranterProject\Backend\Session\SessionInterface
     *   The session, if it exists.
     */
    public function getSession(string $sessionId): ?SessionInterface;

    /**
     * Checks if a session exists.
     *
     * @param string $sessionId
     *   The session id.
     *
     * @return bool
     *   True if it exists.
     */
    public function sessionExists(string $sessionId): bool;

    /**
     * Starts a new session.
     *
     * @param WishgranterProject\Backend\User\UserInterface $user;
     *   The user.
     *
     * @return WishgranterProject\Backend\Session\SessionInterface
     *   The new session.
     */
    public function startNewSession(UserInterface $user, ?int $expiration = null): SessionInterface;
}
