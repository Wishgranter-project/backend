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

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return basename($this->filename, '.jsonl');
    }

    /**
     * {@inheritdoc}
     */
    public function getHash(): string
    {
        return (string) $this->getProperty('hash');
    }

    /**
     * {@inheritdoc}
     */
    public function addSession(string $sessionId, $created, $expiration): void
    {
        $this->getFile()->addObject([
            'sessionId' => $sessionId,
            'created' => $created,
            'expiration' => $expiration,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessions()
    {
        $n = 1;

        do {
            $obj = $this->getFile()->getObject($n);
            $n++;
            yield $obj;
        } while ($obj);
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
     *
     * @return mixed
     *   The property value.
     */
    protected function getProperty(string $name): mixed
    {
        $object = $this->getFile()->getObject(0);
        return $object->{$name} ?? null;
    }
}
