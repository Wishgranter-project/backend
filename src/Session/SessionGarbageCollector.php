<?php

namespace WishgranterProject\Backend\Session;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;

class SessionGarbageCollector
{
    public function __construct(
        protected SessionManager $sessionManager
    ) {
    }

    public function cleanUp()
    {
        foreach ($this->sessionManager->getSessions() as $session) {
            if ($session->expired()) {
                $session->delete();
            }
        }
    }
}
