<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\User\UserInterface;

class UsernameAndPasswordAuthentication extends BaseAuthenticationMethod implements AuthenticationMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUser(ServerRequestInterface $request): ?UserInterface
    {
        $username = $request->post('username');
        $password = $request->post('password');

        if (!$username || !$password) {
            return null;
        }

        if (!$this->userManager->userExists($username)) {
            return null;
        }

        $user = $this->userManager->getUser($username);
        if (!$this->userManager->validatePassword($password, $user->getHash())) {
            return null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function applies(ServerRequestInterface $request): bool
    {
        return $request->getMethod() == 'POST';
    }
}
