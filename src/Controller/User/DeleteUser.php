<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Authentication\AuthenticationManagerInterface;
use WishgranterProject\Backend\Access\AccessResultInterface;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\Token\TokenManager;
use WishgranterProject\Backend\User\UserManager;

class DeleteUser extends UserController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationManagerInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\User\UserManager $userManager
     *   User manager service.
     * @param WishgranterProject\Backend\Token\TokenManager $tokenManager
     *   Token manager service.
     */
    public function __construct(
        protected AuthenticationManagerInterface $authentication,
        protected UserManager $userManager,
        protected TokenManager $tokenManager,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
    {
        return new (get_called_class())(
            $serviceLocator->get('authentication'),
            $serviceLocator->get('userManager'),
            $serviceLocator->get('tokenManager'),
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
        $userId = $request->getAttribute('userId');
        if (!$this->userManager->userExists($userId)) {
            throw new NotFound('User ' . $userId . ' does not exist.');
        }

        $user = $this->userManager->getUser($userId);
        $user->delete();

        return $this->jsonResource([], 200)
            ->addSuccess(200, 'Account deleted.')
            ->renderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $loggedUser = $this->getAuthenticatedUser($request);
        if (!$loggedUser) {
            return $this->accessUnauthenticated();
        }

        $tokenId  = (string) $request->getHeaderLine('X-Confirmation-Token');
        $token    = $this->tokenManager->getToken($tokenId);
        $sameUser = $loggedUser->getId() == $request->getAttribute('userId') || $tokenId;
        $isAdmin  = $loggedUser->hasRole('admin');

        if ($sameUser) {
            $validToken = $token && $this->tokenManager->expendToken($token, $loggedUser, 'deleteOwnAccount');

            return $validToken
                ? $this->accessGranted()
                : $this->accessDenied('You need a valid confirmation token to delete your own account.');
        }

        return $isAdmin
            ? $this->accessGranted()
            : $this->accessDenied('You are not authorized to delete this user.');
    }
}
