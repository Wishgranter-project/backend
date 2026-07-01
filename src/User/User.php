<?php

namespace WishgranterProject\Backend\User;

use AdinanCenci\JsonLines\JsonLines;

class User implements UserInterface
{
    protected ?JsonLines $file = null;

    /**
     * Constructor.
     *
     * @param string $filename
     *   The file where the user's data is stored.
     */
    public function __construct(protected string $filename)
    {
    }

    public function getId(): ?string
    {
        return basename($this->filename, '.jsonl');
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return (string) $this->getProperty('username');
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername(string $username): void
    {
        $this->setProperty('username', $username);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): ?string
    {
        return (string) $this->getProperty('email');
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail(string $email): void
    {
        $this->setProperty('email', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return (array) $this->getProperty('roles', []);
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles): void
    {
        $this->setProperty('roles', $roles);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * {@inheritdoc}
     */
    public function getHash(): ?string
    {
        return (string) $this->getProperty('hash');
    }

    /**
     * {@inheritdoc}
     */
    public function setHash(string $hash): void
    {
        $this->setProperty('hash', $hash);
    }

    /**
     * Return the object to read the user's data.
     *
     * @return AdinanCenci\JsonLines\JsonLines
     *   The json lines file.
     */
    protected function getFile(): JsonLines
    {
        if (!$this->file) {
            $this->file = new JsonLines($this->filename);
        }

        return $this->file;
    }

    /**
     * Retrieve a property from the user.
     *
     * @param string $name
     *   The property name.
     * @param mixed $default
     *   The default if the property is not set.
     *
     * @return mixed
     *   The property value.
     */
    protected function getProperty(string $name, mixed $default = null): mixed
    {
        $object = $this->getFile()->getObject(0);
        return $object->{$name} ?? $default;
    }

    /**
     * Set a property of the user.
     *
     * @param string $name
     *   The property name.
     * @param mixed $value
     *   The value to save.
     *
     * @return void
     */
    protected function setProperty(string $name, mixed $value): void
    {
        $object = file_exists($this->filename)
            ? $this->getFile()->getObject(0)
            : new \stdClass();

        $object->{$name} = $value;

        $this->getFile()->setObject(0, $object);
    }
}
