<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Session\SessionManagerInterface;
use WishgranterProject\Backend\Session\SessionGarbageCollector;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

class Login extends ControllerBase
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\Session\SessionManagerInterface $sessionManager
     *   Session manager service.
     * @param WishgranterProject\Backend\Session\SessionGarbageCollector $sessionGarbageCollector
     *   Session garbage collector.
     */
    public function __construct(
        protected AuthenticationInterface $authentication,
        protected SessionManagerInterface $sessionManager,
        protected SessionGarbageCollector $sessionGarbageCollector
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        $class = get_called_class();

        return new $class(
            $servicesManager->get('authentication'),
            $servicesManager->get('sessionManager'),
            $servicesManager->get('sessionGarbageCollector')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->sessionGarbageCollector->cleanUp();

        $user = $this->authentication
            ->getMethod('session')
            ->getUser($request);

        if ($user) {
            throw new \InvalidArgumentException('You are already logged in');
        }

        $user = $this->authentication->getMethod('usernameAndPassword')->getUser($request);
        if (!$user) {
            throw new \InvalidArgumentException('Username or password incorrect');
        }

        $expiration = strtotime('+24 hours');
        $session = $this->sessionManager->startNewSession($user, $expiration);

        $resource = new JsonResource(['session' => $session->getId()]);
        $resource->addSuccess(200, 'Welcome back');
        $response = $resource->renderResponse();
        $response = $response->withAddedCookie('session', $session->getId(), $expiration);

        return $response;
    }
}
