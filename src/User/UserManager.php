<?php

namespace WishgranterProject\Backend\User;

class UserManager
{
    /**
     * Constructor.
     *
     * @param string $directory
     *   The directory where the users data is stored.
     */
    public function __construct(protected string $directory)
    {
    }

    /**
     * Given a user id, checks if the respective user exists.
     *
     * @param string $userId
     *   The user id.
     *
     * @return bool
     *   True if the user exists.
     */
    public function userExists(string $userId): bool
    {
        return file_exists($this->getFilename($userId));
    }

    /**
     * Given a user id, returns the respective user.
     *
     * @param string $userId
     *   The id.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   User object.
     */
    public function getUser(string $userId): UserInterface
    {
        return new User($this->getFilename($userId));
    }

    /**
     * Given a username, returns the respective user.
     *
     * @param string $username
     *   Username.
     *
     * @return WishgranterProject\Backend\User\UserInterface|null
     *   User object.
     */
    public function getUserByUsername(string $username): ?UserInterface
    {
        $username = strtolower($username);
        foreach ($this->getAllUsers() as $user) {
            if (strtolower($user->getUsername()) == $username) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Given an email, returns the respective user.
     *
     * @param string $email
     *   Username.
     *
     * @return WishgranterProject\Backend\User\UserInterface|null
     *   User object.
     */
    public function getUserByEmail(string $email): ?UserInterface
    {
        $email = strtolower($email);
        foreach ($this->getAllUsers() as $user) {
            if (strtolower($user->getEmail()) == $email) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Return all users to iterate through them.
     */
    public function getAllUsers()
    {
        $entries = scandir($this->directory);
        foreach ($entries as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }

            $file = $this->directory . $entry;
            yield new User($file);
        }
    }

    /**
     * Given a password and a hash, check if they match.
     *
     * @param string $password
     *   Password.
     * @param string $hash
     *   Hash.
     *
     * @return bool
     *   True if they match.
     */
    public function validatePassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generates a hash out of a given password.
     *
     * @param string $password
     *   Password.
     *
     * @return string
     *   The hash.
     */
    public function generateHash(string $password): string
    {
        return password_hash($password, \PASSWORD_DEFAULT);
    }

    /**
     * Given a username, returns an available user id.
     *
     * @param string $username
     *   Username as the base.
     *
     * @return string
     *   An available user id.
     */
    public function getAvailableUserId(string $username): string
    {
        $userId = $this->usernameToUserId($username);
        while ($this->userExists($userId)) {
            $userId = preg_match('/_(\d+)$/', $userId, $matches)
                ? preg_replace('/_(\d+)$/', '_' . $matches[1], $userId)
                : $userId = '_2';
        }

        return $userId;
    }

    /**
     * Given a username, returns a valid user id.
     *
     * @param string $username
     *   Username as the base.
     *
     * @return string
     *   A valid user id.
     */
    protected function usernameToUserId(string $username): string
    {
        $userId = strtolower(trim($username));
        $userId = str_replace(' ', '-', $userId);
        $userId = preg_replace('/[^\w\-]/', '', $userId);
        $userId = $userId
            ? $userId
            : ((string) rand(10000, 99999));

        return $userId;
    }

    /**
     * Checks if a string is a valid username.
     *
     * @param string $username
     *   The username candidate.
     *
     * @return bool
     *   True if valid.
     */
    public function validateUsername(string $username): bool
    {
        if (trim($username) != $username) {
            return false;
        }

        return preg_replace('/[^\w\d\- ]/', '', $username) == $username;
    }

    /**
     * Given a user id, retrieves the filename containing the user's data.
     *
     * @param string $userId
     *   User id.
     *
     * @return string
     *   Absolute filename.
     */
    protected function getFilename(string $userId): string
    {
        return $this->directory . $userId . '.jsonl';
    }
}
