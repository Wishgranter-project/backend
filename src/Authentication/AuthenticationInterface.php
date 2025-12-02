<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\User\UserInterface;

interface AuthenticationInterface
{
    /**
     * Given a HTTP request, returns the relevant user.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\Backend\User\UserInterface
     *   The user.
     */
    public function getUser(ServerRequestInterface $request): ?UserInterface;

    /**
     * Given a name, returns the matching authentication method.
     *
     * @param string $methodName
     *   The name of the method.
     *
     * @return null|WishgranterProject\Backend\Authentication\AuthenticationMethodInterface
     *   Authentication method.
     */
    public function getMethod(string $methodName): ?AuthenticationMethodInterface;
}
