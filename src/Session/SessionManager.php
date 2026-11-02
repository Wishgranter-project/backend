<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\Backend\UserGrant\UserGrantManager;
use WishgranterProject\Backend\UserGrant\UserGrantInterface;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

class SessionManager extends UserGrantManager implements SessionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSession(string $sessionId): ?SessionInterface
    {
        return $this->getGrant($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function sessionExists(string $sessionId): bool
    {
        return $this->grantExists($sessionId);
    }

    /**
     * Return all sessions to iterate through them.
     */
    public function getSessions()
    {
        return $this->getGrants();
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
     * Instantiates a session object from a file.
     *
     * @param string $filename
     *   The file containing the session.
     *
     * @return WishgranterProject\Backend\UserGrant\UserGrantInterface
     *   The session object.
     */
    protected function getGrantFromFile(string $filename): UserGrantInterface
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
