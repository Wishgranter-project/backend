<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;

class SessionGarbageCollector
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Session\SessionManagerInterface $sessionManager
     *   Session manager service.
     */
    public function __construct(
        protected SessionManagerInterface $sessionManager
    ) {
    }

    /**
     * Clean up expired sessions.
     */
    public function cleanUp()
    {
        foreach ($this->sessionManager->getSessions() as $session) {
            if ($session->expired()) {
                $session->delete();
            }
        }
    }
}
