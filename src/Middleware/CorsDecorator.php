<?php

namespace WishgranterProject\Backend\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Preflight;

/**
 * Decorates responses with CORS headers.
 */
class CorsDecorator extends Preflight
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (!$response->hasHeader('Access-Control-Allow-Origin')) {
            $response = $this->withAddedCorsHeaders($response);
        }

        return $response;
    }
}
