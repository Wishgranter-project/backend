<?php

namespace WishgranterProject\Backend\Controller\User;

use WishgranterProject\Backend\Access\AccessResultInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\User\UserManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreateUser extends AuthenticatedController
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

    /**
     * Invoke the controler.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = $this->getPostData($request);

        $this->validateData($data);


        

        return $this->jsonResource($data)
            ->renderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getUser($request);
        if (!$user /** and $anonymous_registration_enabled */) {
            return $this->accessGranted();
        }

        /**
         * @todo
         *   Replace this for a proper permission system call.
         */
        return $user->getId() == 'adinan'
            ? $this->accessGranted()
            : $this->accessUnauthorized('You are already logged in');
    }

    protected function validateData($data)
    {

    }
}
