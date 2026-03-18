<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserInterface;

class Session implements SessionInterface
{
    public function __construct(
        protected string $filename,
        protected ?UserInterface $user,
        protected int $created,
        protected int $expiration,
    ) {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getId(): string
    {
        return basename($this->filename, '.json');
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getExpiration(): int
    {
        return $this->expiration;
    }


    public function expired(): bool
    {
        return time() >= $this->expiration;
    }

    public function save()
    {
        $data = [
            'userId' => $this->user->getUsername(),
            'created' => $this->created,
            'expiration' => $this->expiration,
        ];

        $json = json_encode($data);

        file_put_contents($this->filename, $json);
    }

    public function delete()
    {
        unlink($this->filename);
    }
}
