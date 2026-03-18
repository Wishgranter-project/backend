<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\Method\AuthenticationMethodInterface;
use WishgranterProject\Backend\Authentication\Method\SessionAuthentication;
use WishgranterProject\Backend\Authentication\Method\UsernameAndPasswordAuthentication;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\UserInterface;

/**
 * Authentication service.
 */
class AuthenticationManager implements AuthenticationInterface
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
        foreach ($this->getApplicableMethods() as $method) {
            if ($user = $method->getUser($request)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicableMethods(ServerRequestInterface $request): array
    {
        return array_filter($this->getMethods(), function ($method) use($request) {
            return $method->applies($request);
        });
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
            'session'             => SessionAuthentication::instantiate($this->serviceManager),
            'usernameAndPassword' => UsernameAndPasswordAuthentication::instantiate($this->serviceManager),
        ];
    }
}
