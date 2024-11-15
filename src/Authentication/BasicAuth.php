<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;

class BasicAuth implements AuthenticationMethodInterface
{
    protected $userManager;

    public function __construct($userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUser(ServerRequestInterface $request): ?User
    {
        $serverParams = $request->getServerParams();

        $userName = $serverParams['PHP_AUTH_USER'] ?? null;
        $password = $serverParams['PHP_AUTH_PW'] ?? null;

        if (!$userName || !$password) {
            return null;
        }

        foreach ($this->userManager->getAllUsers() as $user) {
            if ($user->validate($password)) {
                return $user;
            }
        }

        return null;
    }

}
