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
        return $handler->responseFactory->ok('-');
    }

    /**
     * @return WishgranterProject\Backend\Helper\JsonResource
     *   JsonResource object.
     */
    public function jsonResource(null|array|\stdClass $data = null, int $statusCode = 200): JsonResource
    {
        return new JsonResource($data, $statusCode);
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
     * Returns the parsed body of the request.
     *
     * Most of the time will return the contents of $_POST.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return array
     *   The parsed body.
     */
    public static function getPostData(ServerRequestInterface $request): array
    {
        $contentType = $request->getHeaderLine('content-type');

        if (! $contentType) {
            return [];
        }

        $mime = self::getMime((string) $contentType);

        if (! in_array($mime, ['application/x-www-form-urlencoded', 'multipart/form-data', ''])) {
            return [];
        }

        return $request->getParsedBody() ?? [];
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
    public static function getMime(string $contentType): string
    {
        return preg_match('#^([^;]+)#', $contentType, $matches)
            ? trim(strtolower($matches[1]))
            : $contentType;
    }
}
