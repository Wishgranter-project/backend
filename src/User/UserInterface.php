<?php

namespace WishgranterProject\Backend\User;

interface UserInterface
{
    /**
     * Return the user's unique id.
     *
     * @return string|null
     *   The user id.
     */
    public function getId(): ?string;

    /**
     * Return the user's username.
     *
     * @return string|null
     *   The username.
     */
    public function getUsername(): ?string;

    /**
     * Set the username.
     *
     * @param string $username
     *   The username.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   Itself.
     */
    public function setUsername(string $username): UserInterface;

    /**
     * Return the user's e-mail.
     *
     * @return string
     *   E-mail.
     */
    public function getEmail(): ?string;

    /**
     * Set the email.
     *
     * @param string $email
     *   The email.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   Itself.
     */
    public function setEmail(string $email): UserInterface;

    /**
     * Returns the user's role within the system.
     *
     * @return string[]
     *   Array of strings with the roles.
     */
    public function getRoles(): array;

    /**
     * Set the roles.
     *
     * @param string[] $roles
     *   The roles.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   Itself.
     */
    public function setRoles(array $roles): UserInterface;

    /**
     * Checks if the user has been granted a given role.
     *
     * @param string $role
     *   The role.
     *
     * @return bool
     *   True if the user has the role.
     */
    public function hasRole(string $role): bool;

    /**
     * Return the hash for the user's password.
     *
     * @return string|null
     *   Hash.
     */
    public function getHash(): ?string;

    /**
     * Set the password hash.
     *
     * @param string $hash
     *   The password hash.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   Itself.
     */
    public function setHash(string $hash): UserInterface;

    /**
     * Returns the timestamp of when the account was created.
     *
     * @return int
     *   Unix timestamp.
     */
    public function getCreated(): int;

    /**
     * Sets the timestamp of when the account was created.
     *
     * @param int $timestamp
     *   Unix timestamp.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   Itself.
     */
    public function setCreated(int $timestamp): UserInterface;

    /**
     * Returns the timestamp of the last time the user logged in.
     *
     * @return int
     *   Unix timestamp.
     */
    public function getLastLogin(): int;

    /**
     * Sets the timestamp of when the last time the user logged in.
     *
     * @param int $timestamp
     *   Unix timestamp.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   Itself.
     */
    public function setLastLogin(int $timestamp): UserInterface;
}
