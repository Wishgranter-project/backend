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
     * Given a user id, returns the user.
     *
     * @param string $userId
     *   The id.
     *
     * @return WishgranterProject\Backend\User\User
     *   User object.
     */
    public function getUser(string $userId): User
    {
        return new User($this->getFilename($userId));
    }

    public function getUserByUsername(string $username): ?User
    {
        foreach ($this->getAllUsers() as $user) {
            if ($user->getUsername() == $username) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Given a user id, checks if it exists.
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
        return $this->directory . '/' . $userId . '.jsonl';
    }
}
