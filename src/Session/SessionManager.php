<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;

class SessionManager implements SessionManagerInterface
{
    public function __construct(
        protected string $directory,
        protected UserManager $userManager,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getSession(string $sessionId): ?SessionInterface
    {
        if (!$this->sessionExists($sessionId)) {
            return null;
        }

        $filename = $this->getFilename($sessionId);
        return $this->getSessionFromFile($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function sessionExists(string $sessionId): bool
    {
        $filename = $this->getFilename($sessionId);
        return file_exists($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function startNewSession(UserInterface $user, ?int $expiration = null): SessionInterface
    {
        $sessionId = \WishgranterProject\DescriptivePlaylist\Utils\Helpers::guidv4();
        $filename  = $this->getFilename($sessionId);
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
     * @return WishgranterProject\Backend\Session\SessionInterface
     *   The session object.
     */
    protected function getSessionFromFile(string $filename): SessionInterface
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

    /**
     * Given a session id, retrieves the filename containing the session's data.
     *
     * @param string $sessionId
     *   Session id.
     *
     * @return string
     *   Absolute filename.
     */
    protected function getFilename(string $sessionId): string
    {
        return $this->directory . '/' . $sessionId . '.json';
    }
}
