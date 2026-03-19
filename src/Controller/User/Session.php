<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Authentication\Authentication;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

/**
 * Used to check if the user is authenticated.
 */
class Session extends Login
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authentication
            ->getMethod('session')
            ->getUser($request);

        if ($user) {
            $resource = new JsonResource(null, 200);
            $resource->addSuccess(200, 'Welcome back ' . $user->getUsername());
        } else {
            $resource = new JsonResource(null, 400);
            $resource->addError(400, 'Unauthenticated');
        }

        $response = $resource->renderResponse();
        //$response = $response->withAddedCookie('session', $session, $expiration);

        return $response;
    }
}
