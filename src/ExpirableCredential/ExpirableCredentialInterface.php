<?php

namespace WishgranterProject\Backend\ExpirableCredential;

use WishgranterProject\Backend\User\UserInterface;

/**
 * Represents a credential with an expiration date.
 */
interface ExpirableCredentialInterface
{
    /**
     * Returns the credential's id.
     *
     * @return string
     *   The credential id.
     */
    public function getId(): string;

    /**
     * The user that owns the credential and gets to use it.
     *
     * @return null|WishgranterProject\Backend\User\UserInterface
     *   The user.
     */
    public function getUser(): ?UserInterface;

    /**
     * Returns the timestamp of when the credential was created.
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
     * Returns the lenght of time the credential is valid for.
     *
     * @return int
     *   Time in seconds
     */
    public function getTimeToLive(): int;

    /**
     * Returns the current age of the credential.
     *
     * @return int
     *   Age in seconds.
     */
    public function getAge(): int;

    /**
     * Returns the time the credential has left.
     *
     * @return int
     *   The remaining time in seconds.
     */
    public function getTimeLeft(): int;

    /**
     * Checks if the credential is expired.
     *
     * @return bool
     *   True if expired.
     */
    public function isExpired(): bool;

    /**
     * Saves the credential.
     */
    public function save(): void;

    /**
     * Deletes the credential.
     */
    public function delete(): void;
}
