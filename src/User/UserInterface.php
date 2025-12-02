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

    /**
     * Opens a new session for the user.
     *
     * @param string $sessionId
     *   The session id.
     * @param int $created
     *   Timestamp.
     * @param int $expiration
     *   Timestamp.
     */
    public function addSession(string $sessionId, $created, $expiration);

    /**
     * Iterates through the user's sessions.
     */
    public function getSessions();
}
