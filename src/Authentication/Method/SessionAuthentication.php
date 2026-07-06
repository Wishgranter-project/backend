<?php

namespace WishgranterProject\Backend\Authentication\Method;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\User\User;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\Session\SessionInterface;

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
    public static function instantiate(ServiceLocator $serviceLocator): AuthenticationMethodInterface
    {
        return new (get_called_class())(
            $serviceLocator->get('userManager'),
            $serviceLocator->get('sessionManager'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(ServerRequestInterface $request, &$session = null): ?User
    {
        if (!$session = $this->getSession($request)) {
            return null;
        }

        if ($session->expired() || !$session->getUser()) {
            $session->delete();
            $session = null;
            return null;
        }

        return $session->getUser();
    }

    /**
     * Retrieves the session referenced by the request.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request.
     *
     * @return null|WishgranterProject\Backend\Session\SessionInterface
     *   The session referenced in the request.
     */
    public function getSession(ServerRequestInterface $request): ?SessionInterface
    {
        if (!$sessionId = $this->getSessionId($request)) {
            return null;
        }

        return $this->sessionManager->getSession($sessionId);
    }

    /**
     * Retrieves the session id in the request.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request.
     *
     * @return string|null
     *   The session id within the request.
     */
    protected function getSessionId(ServerRequestInterface $request): ?string
    {
        $cookies = $request->getCookieParams();

        if (isset($cookies['session']) && !is_null($cookies['session'])) {
            return $cookies['session'];
        }

        if (IS_TEST_ENVIRONMENT && $sessionId = $request->getHeaderLine('test-environment-only-session-id')) {
            return $sessionId;
        }

        return null;
    }
}
