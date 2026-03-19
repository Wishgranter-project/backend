<?php

namespace WishgranterProject\Backend\Authentication\Method;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\User;
use WishgranterProject\Backend\User\UserManager;

class SessionAuthentication extends BaseAuthenticationMethod implements AuthenticationMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        protected UserManager $userManager,
        protected $sessionManager
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): AuthenticationMethodInterface
    {
        return new (get_called_class())(
            $servicesManager->get('userManager'),
            $servicesManager->get('sessionManager'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(ServerRequestInterface $request): ?User
    {
        if (!$sessionId = $this->getSessionId($request)) {
            return null;
        }

        if (!$session = $this->sessionManager->getSession($sessionId)) {
            return null;
        }

        if ($session->expired() || !$session->getUser()) {
            $session->delete();
            return null;
        }

        return $session->getUser();
    }

    protected function getSessionId(ServerRequestInterface $request): ?string
    {
        $cookies = $request->getCookieParams();

        $sessionCookie = isset($cookies['session'])
            ? $cookies['session']
            : null;

        $sessionId = $sessionCookie ?:
            $request->getHeaderLine('Authorization') ?:
            null;

        return $sessionId;
    }
}
