<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\Backend\ExpirableCredential\ExpirableCredentialManager;
use WishgranterProject\Backend\ExpirableCredential\ExpirableCredentialInterface;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

class SessionManager extends ExpirableCredentialManager implements SessionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSession(string $sessionId): ?SessionInterface
    {
        return $this->getCredential($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function sessionExists(string $sessionId): bool
    {
        return $this->credentialExists($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessions()
    {
        return $this->getCredentials();
    }

    /**
     * {@inheritdoc}
     */
    public function startNewSession(UserInterface $user, ?int $expiration = null): SessionInterface
    {
        $sessionId  = Helpers::guidv4();
        $filename   = $this->getFilename($sessionId);
        $expiration = $expiration
            ? $expiration
            : strtotime('+24 hours');

        $session = new Session(
            $filename,
            $user,
            time(),
            $expiration,
        );

        $session->save();

        return $session;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCredentialFromFile(string $filename): ExpirableCredentialInterface
    {
        $contents = file_get_contents($filename);
        $data     = json_decode($contents);

        $session = new Session(
            $filename,
            $this->userManager->getUser($data->userId),
            $data->created,
            $data->expiration,
        );

        return $session;
    }
}
