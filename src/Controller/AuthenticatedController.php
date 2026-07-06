<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Exception\Unauthorized;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\Backend\Access\AccessResultInterface;

abstract class AuthenticatedController extends ControllerBase
{
    /**
     * The user authenticated.
     *
     * @var null|false|WishgranterProject\Backend\User\UserInterface
     */
    protected $authenticatedUser = false;

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
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
    {
        return new (get_called_class())($serviceLocator->get('authentication'));
    }

    /**
     * Retrieves the current user.
     *
     * @param null|Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return null|WishgranterProject\Backend\User\UserInterface
     *   The user, NULL if no user can be matched.
     */
    public function getAuthenticatedUser(?ServerRequestInterface $request): ?UserInterface
    {
        if ($this->authenticatedUser === false) {
            $this->authenticatedUser = $this->authentication->getUser($request);
        }

        return $this->authenticatedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getAuthenticatedUser($request);

        return $user
            ? $this->accessGranted()
            : $this->accessUnauthenticated();
    }
}
