<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

class Login extends ControllerBase
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
        return new Login($servicesManager->get('authentication'));
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authentication->getMethod('session')->getUser($request);
        if ($user) {
            throw new \InvalidArgumentException('You are already logged in');
        }

        $user = $this->authentication->getMethod('usernameAndPassword')->getUser($request);
        if (!$user) {
            throw new \InvalidArgumentException('Username or password incorrect');
        }

        $session = Helpers::guidv4();
        $now = time();
        $expiration = strtotime('+24 hours');

        $user->addSession($session, $now, $expiration);

        $resource = new JsonResource();
        $resource->addSuccess(200, 'Welcome back');
        $response = $resource->renderResponse();
        $response = $response->withAddedCookie('session', $session, $expiration);

        return $response;
    }
}
