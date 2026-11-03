<?php

namespace WishgranterProject\Backend\Controller\Confirmation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Authentication\AuthenticationManagerInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\Token\TokenManager;

/**
 * Security confirmation.
 *
 * Generates a unique, short-lived token to be used in sensitive requests,
 * mainly destructive ones.
 */
class Confirmation extends AuthenticatedController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationManagerInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\User\UserManager $userManager
     *   The user manager service.
     * @param WishgranterProject\Backend\Token\TokenManager $tokenManager
     *   The token manager service.
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
        $called = get_called_class();
        return new $called(
            $serviceLocator->get('authentication'),
            $serviceLocator->get('userManager'),
            $serviceLocator->get('tokenManager'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->tokenManager->cleanUp();

        $action          = $request->post('action');
        $password        = $request->post('password');
        $confirmPassword = $request->post('confirmPassword');
        $user            = $this->getAuthenticatedUser($request);

        if ($password != $confirmPassword) {
            throw new \InvalidArgumentException('Password and confirmation do not match.');
        }

        if (!$this->userManager->validatePassword($password, $user->getHash())) {
            throw new \InvalidArgumentException('Password incorrect.');
        }

        $expiration = strtotime('+5 minutes');
        $token = $this->tokenManager->issueNewToken($user, $action, $expiration);

        $data = $token->dataTransfer();
        $data['id'] = $token->getId();

        $resource = new JsonResource($data, 200);
        $resource->addSuccess(200, 'Token created', 'It shall expire in the next five minutes.');

        return $resource->renderResponse();
    }
}
