<?php

namespace WishgranterProject\Backend\Controller\User;

use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Access\AccessResultInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetUser extends AuthenticatedController
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
        $user = $this->getUser($request);

        $data = $this->dataTransferItem($user);

        return $this->jsonResource($data)
            ->renderResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getUser($request);
        if (!$user) {
            return $this->accessUnauthenticated();
        }

        return $user->getId() == $request->getAttribute('userId')
            ? $this->accessGranted()
            : $this->accessUnauthorized('You are not authorized to see this user\'s info');
    }

    protected function dataTransferItem($user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ];
    }
}
