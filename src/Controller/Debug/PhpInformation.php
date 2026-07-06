<?php

namespace WishgranterProject\Backend\Controller\Debug;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Access\AccessResultInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;

class PhpInformation extends AuthenticatedController
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
        ob_start();
        phpinfo();
        $html = ob_get_contents();
        ob_end_clean();

        return $handler->responseFactory->ok($html);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return $this->accessUnauthenticated();
        }

        return $user->hasRole('admin')
            ? $this->accessGranted()
            : $this->accessDenied();
    }
}
