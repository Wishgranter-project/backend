<?php

namespace WishgranterProject\Backend\Controller\Session;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Authentication\Authentication;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

/**
 * Used to check if the user is authenticated.
 */
class GetSession extends OpenSession
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->sessionGarbageCollector->cleanUp();

        $user = $this->authentication
            ->getMethod('session')
            ->getUser($request, $session);

        if (!$user) {
            $resource = new JsonResource(null, 401);
            $resource->addError(401, 'Unauthenticated');
            return $resource->renderResponse();
        }

        $resource = new JsonResource([
            'expiration' => $session->getExpiration(),
            'id'         => $user->getId(),
            'username'   => $user->getUsername(),
        ], 200);

        if (IS_TEST_ENVIRONMENT) {
            // Add the session id to the body so JS scripts may read it.
            $resource->setData('test-environment-only-session-id', $session->getId());
        }

        $resource->addSuccess(200, 'Welcome back ' . $user->getUsername());

        $response = $resource->renderResponse();
        if (IS_TEST_ENVIRONMENT) {
            // Add the session id to a non-cookie header so JS scripts may read it.
            $response = $response->withAddedHeader('test-environment-only-session-id', $session->getId());
        }

        return $response;
    }
}
