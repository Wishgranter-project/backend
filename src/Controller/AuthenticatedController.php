<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Exception\Unauthorized;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\User;
use WishgranterProject\Backend\Access\AccessResultInterface;

abstract class AuthenticatedController extends ControllerBase
{
    protected $user = false;

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
     * @param null|Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return null|WishgranterProject\Backend\User\User
     *   The user, NULL if no user can be matched.
     */
    public function getUser(?ServerRequestInterface $request): ?User
    {
        if ($this->user === false) {
            $this->user = $this->authentication->getUser($request);
        }

        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getUser($request);

        return $user
            ? $this->accessGranted()
            : $this->accessForbidden();
    }
}
