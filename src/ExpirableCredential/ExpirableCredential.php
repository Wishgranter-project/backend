<?php

namespace WishgranterProject\Backend\ExpirableCredential;

use WishgranterProject\Backend\User\UserInterface;

abstract class ExpirableCredential implements ExpirableCredentialInterface
{
    /**
     * Constructor.
     *
     * @param string $filename
     *   Absolute filename that stores the grant.
     * @param null|WishgranterProject\Backend\User\UserInterface
     *   The user that owns the grant and gets to use it.
     * @param int $created
     *   Timestamp of when the grant was created.
     * @param int $expiration
     *   Expiration timestamp.
     */
    public function __construct(
        protected string $filename,
        protected ?UserInterface $user,
        protected int $created,
        protected int $expiration,
    ) {
    }

    /**
     * Returns the absolute filename that stores the grant.
     *
     * @return string
     *   Absolute filename.
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return basename($this->filename, '.json');
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreated(): int
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiration(): int
    {
        return $this->expiration;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeToLive(): int
    {
        return $this->expiration - $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getAge(): int
    {
        return time() - $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeLeft(): int
    {
        return time() >= $this->expiration
            ? 0
            : $this->expiration - time();
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired(): bool
    {
        return time() >= $this->expiration;
    }

    /**
     * {@inheritdoc}
     */
    public function save(): void
    {
        $data = $this->dataTransfer();

        $json = json_encode($data);

        file_put_contents($this->filename, $json);
    }

    /**
     * Generates a data transfer array.
     *
     * @return array
     *   Data about the grant.
     */
    public function dataTransfer(): array
    {
        return [
            'userId'     => $this->user->getId(),
            'username'   => $this->user->getUsername(),
            'created'    => $this->created,
            'expiration' => $this->expiration,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): void
    {
        unlink($this->filename);
    }
}
