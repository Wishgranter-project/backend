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
     * Given a username, returns the user.
     *
     * @param string $username
     *   The username.
     *
     * @return WishgranterProject\Backend\User\User
     *   User object.
     */
    public function getUser(string $username): User
    {
        return new User($this->getFilename($username));
    }

    /**
     * Given a username, checks if it exists.
     *
     * @param string $username
     *   The username.
     *
     * @return bool
     *   True if the user exists.
     */
    public function userExists(string $username): bool
    {
        return file_exists($this->getFilename($username));
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
        return md5($password) == $hash;
    }

    /**
     * Given a username, retrieves the filename containing the user's data.
     *
     * @param string $username
     *   Username.
     *
     * @return string
     *   Absolute filename.
     */
    protected function getFilename(string $username): string
    {
        return $this->directory . '/' . $username . '.jsonl';
    }
}
