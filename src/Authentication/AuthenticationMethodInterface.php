<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\UserInterface;

interface AuthenticationMethodInterface
{
    /**
     * Instantiates a new instance of this class.
     *
     * @param WishgranterProject\Backend\Service\ServicesManager $serviceManager
     *   The service manager for dependency injection.
     *
     * @return WishgranterProject\Backend\Authentication\AuthenticationMethodInterface
     *   The authentication object.
     */
    public static function instantiate(ServicesManager $servicesManager): AuthenticationMethodInterface;

    /**
     * Check if this method applies to a given request.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   HTTP request.
     *
     * @return bool
     *   True if it applies.
     */
    public function applies(ServerRequestInterface $request): bool;

    /**
     * Retrieves the user.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   HTTP request.
     *
     * @return null|WishgranterProject\Backend\User\UserInterface
     *   User.
     */
    public function getUser(ServerRequestInterface $request): ?UserInterface;
}
