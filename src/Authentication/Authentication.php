<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\UserInterface;

/**
 * Authentication service.
 */
class Authentication implements AuthenticationInterface
{
    /**
     * Constructor
     *
     * @param WishgranterProject\Backend\Service\ServicesManager $serviceManager
     *   Services manager.
     */
    public function __construct(protected ServicesManager $serviceManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(ServerRequestInterface $request): ?UserInterface
    {
        foreach ($this->getMethods() as $method) {
            if (!$method->applies($request)) {
                continue;
            }

            $user = $method->getUser($request);
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(string $methodName): ?AuthenticationMethodInterface
    {
        return $this->getMethods()[$methodName] ?? null;
    }

    /**
     * Returns all available authentication methods.
     *
     * @return array
     *   Array of authentication methods.
     */
    public function getMethods(): array
    {
        return [
            'session' => SessionAuthentication::instantiate($this->serviceManager),
            'usernameAndPassword' => UsernameAndPasswordAuthentication::instantiate($this->serviceManager),
        ];
    }
}
