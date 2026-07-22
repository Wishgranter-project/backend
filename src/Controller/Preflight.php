<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Service\ServiceLocator;

/**
 * Preflight controller.
 */
class Preflight extends ControllerBase
{
    public function __construct(protected $settings)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
    {
        $class = get_called_class();
        return new $class($serviceLocator->get('settings'));
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->responseFactory->ok('');
        $response = $this->withAddedCorsHeaders($response);

        return $response;
    }

    /**
     * Add CORS headers to a response.
     *
     * @param Psr\Http\Message\ResponseInterface $response
     *   The response object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response with cors headers added.
     */
    protected function withAddedCorsHeaders(ResponseInterface $response): ResponseInterface
    {
        $response = $response->withAddedHeader('Access-Control-Allow-Origin', $this->settings->get('corsAllowedDomain', '*'));
        $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
        $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'content-type, *');
        $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');

        return $response;
    }
}
