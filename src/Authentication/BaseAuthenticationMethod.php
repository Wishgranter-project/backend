<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\Backend\User\UserManager;

abstract class BaseAuthenticationMethod implements AuthenticationMethodInterface
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\User\UserManager $userManager
     *   The user manager service.
     */
    public function __construct(protected UserManager $userManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): AuthenticationMethodInterface
    {
        return new (get_called_class())($servicesManager->get('userManager'));
    }

    /**
     * {@inheritdoc}
     */
    public function applies(ServerRequestInterface $request): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(ServerRequestInterface $request): ?UserInterface
    {
        return null;
    }
}
