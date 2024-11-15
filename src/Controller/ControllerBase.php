<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Exception\NotFound;

abstract class ControllerBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Implementation specific.
    }

    /**
     * Invoke the controler to generate a response.
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
        $response = $this->generateResponse($request, $handler);

        if (! $response instanceof ResponseInterface) {
            throw new \Exception('Controller failed to return a response');
        }

        return $response;
    }

    /**
     * Instantiates a new controller.
     *
     * @param WishgranterProject\Backend\Service\ServicesManager $serviceManager
     *   The service manager for dependency injection.
     *
     * @return WishgranterProject\Backend\Controller\ControllerBase
     *   The instantiated controller.
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new \get_called_class();
    }

    /**
     * Instantiates and invokes the controller.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    public static function respond(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $servicesManager = ServicesManager::singleton();
        $controller = get_called_class()::instantiate($servicesManager);

        return $controller($request, $handler);
    }

    /**
     * Generate a response.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    protected function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Implementation specific.
    }

    /**
     * Returns the parsed body of the request.
     * Most of the time will return the contents of $_POST.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return array
     *   The parsed body.
     */
    protected function getPostData(ServerRequestInterface $request): array
    {
        $contentType = $request->getHeaderLine('content-type');

        if (! $contentType) {
            return [];
        }

        $mime = $this->getMime((string) $contentType);

        if (! in_array($mime, ['application/x-www-form-urlencoded', 'multipart/form-data', ''])) {
            return [];
        }

        return $request->getParsedBody();
    }

    /**
     * Extract the mime from a content-type header.
     *
     * @param string $contentType
     *   The content type header.
     *
     * @return string
     *   The mime.
     */
    private static function getMime(string $contentType): string
    {
        return preg_match('#^([^;]+)#', $contentType, $matches)
            ? trim(strtolower($matches[1]))
            : $contentType;
    }
}
