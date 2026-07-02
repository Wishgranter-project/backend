<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Access\AccessResultInterface;
use WishgranterProject\Backend\Exception\NotFound;

class GetUser extends UserController
{
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
        $data = $this->dataTransferUser($user);
        return $this->jsonResource($data)
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

        $sameUser = $loggedUser->getId() == $request->getAttribute('userId');
        $isAdmin = $loggedUser->hasRole('admin');

        return $sameUser || $isAdmin
            ? $this->accessGranted()
            : $this->accessUnauthorized('You are not authorized to see this user\'s info');
    }
}
