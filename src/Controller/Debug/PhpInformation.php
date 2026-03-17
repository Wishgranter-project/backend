<?php

namespace WishgranterProject\Backend\Controller\Debug;

use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServicesManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
        $this->needsAnUser($request);

        ob_start();
        phpinfo();
        $html = ob_get_contents();
        ob_end_clean();

        return $handler->responseFactory->ok($html);
    }
}
