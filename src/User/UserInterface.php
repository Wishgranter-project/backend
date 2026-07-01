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
     */
    public function setUsername(string $username): void;

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
     */
    public function setEmail(string $email): void;

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
     */
    public function setRoles(array $roles): void;

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
     */
    public function setHash(string $hash): void;
}
