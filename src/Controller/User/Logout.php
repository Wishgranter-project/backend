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

class Logout extends ControllerBase
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $session = Helpers::guidv4();
        $expiration = 1;

        $resource = new JsonResource();
        $resource->addSuccess(200, 'Goodbye');
        $response = $resource->renderResponse();
        $response = $response->withAddedCookie('session', $session, $expiration);

        return $response;
    }
}
