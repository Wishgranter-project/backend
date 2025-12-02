<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Exception\Unauthorized;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\User;

abstract class AuthenticatedController extends ControllerBase
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationInterface $authentication
     *   Authentication service.
     */
    public function __construct(protected AuthenticationInterface $authentication)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new (get_called_class())($servicesManager->get('authentication'));
    }

    /**
     * Retrieves the current user.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return null|WishgranterProject\Backend\User\User
     *   The user.
     */
    public function getUser(ServerRequestInterface $request): ?User
    {
        return $this->authentication->getUser($request);
    }

    /**
     * Retrieves the current user.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return null|WishgranterProject\Backend\User\User
     *   The user.
     */
    public function needsAnUser(
        ServerRequestInterface $request,
        string $message = 'You are unauthorized to access this page'
    ): ?User {
        $user = $this->getUser($request);
        if (!$user) {
            throw new Unauthorized($message);
        }

        return $user;
    }
}
