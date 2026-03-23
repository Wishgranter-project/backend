<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserInterface;

interface SessionInterface
{
    /**
     * Returns the session's id.
     *
     * @return string
     *   The session id.
     */
    public function getId(): string;

    /**
     * Returns the user the session belongs to.
     *
     * @return null|WishgranterProject\Backend\User\UserInterface
     *   The user.
     */
    public function getUser(): ?UserInterface;

    /**
     * Returns the timestamp of when the session was created.
     */
    public function getCreated(): int;

    /**
     * Returns the expiration timestamp.
     *
     * @return int
     *   Timestamp.
     */
    public function getExpiration(): int;

    /**
     * Returns the lenght of time the session was granted.
     *
     * @return int
     *   Time in seconds
     */
    public function getTimeToLive(): int;

    /**
     * Returns the current age of the session.
     *
     * @return int
     *   Age in seconds.
     */
    public function getAge(): int;

    /**
     * Returns the time the session has left.
     *
     * @return int
     *   The remaining time in seconds.
     */
    public function getTimeLeft(): int;

    /**
     * Checks if the session is expired.
     *
     * @return bool
     *   True if expired.
     */
    public function expired(): bool;

    /**
     * Saves the session.
     */
    public function save(): void;

    /**
     * Deletes the session.
     */
    public function delete(): void;
}
