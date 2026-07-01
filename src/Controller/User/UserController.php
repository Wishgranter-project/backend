<?php

namespace WishgranterProject\Backend\Controller\User;

use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\Service\ServiceLocator;

abstract class UserController extends AuthenticatedController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\User\UserManager $userManager
     *   User manager service.
     */
    public function __construct(
        protected AuthenticationInterface $authentication,
        protected UserManager $userManager
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
    {
        return new (get_called_class())(
            $serviceLocator->get('authentication'),
            $serviceLocator->get('userManager')
        );
    }

    protected function dataTransferUser(UserInterface $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ];
    }
}
