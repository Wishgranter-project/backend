<?php

namespace WishgranterProject\Backend\UserGrant;

use WishgranterProject\Backend\User\UserInterface;

interface UserGrantInterface
{
    /**
     * Returns the grant's id.
     *
     * @return string
     *   The grant id.
     */
    public function getId(): string;

    /**
     * The user that owns the grant and gets to use it.
     *
     * @return null|WishgranterProject\Backend\User\UserInterface
     *   The user.
     */
    public function getUser(): ?UserInterface;

    /**
     * Returns the timestamp of when the grant was created.
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
     * Returns the lenght of time the grant is valid for.
     *
     * @return int
     *   Time in seconds
     */
    public function getTimeToLive(): int;

    /**
     * Returns the current age of the grant.
     *
     * @return int
     *   Age in seconds.
     */
    public function getAge(): int;

    /**
     * Returns the time the grant has left.
     *
     * @return int
     *   The remaining time in seconds.
     */
    public function getTimeLeft(): int;

    /**
     * Checks if the grant is expired.
     *
     * @return bool
     *   True if expired.
     */
    public function isExpired(): bool;

    /**
     * Saves the grant.
     */
    public function save(): void;

    /**
     * Deletes the grant.
     */
    public function delete(): void;
}
