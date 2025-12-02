<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\User;
use WishgranterProject\Backend\User\UserManager;

class SessionAuthentication extends BaseAuthenticationMethod implements AuthenticationMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUser(ServerRequestInterface $request): ?User
    {
        $sessionId = $request->getCookieParams()['session'] ?? null;
        if (!$sessionId) {
            return null;
        }

        foreach ($this->userManager->getAllUsers() as $user) {
            foreach ($user->getSessions() as $session) {
                if ($session->sessionId == $sessionId) {
                    return $user;
                }
            }
        }

        return null;
    }
}
