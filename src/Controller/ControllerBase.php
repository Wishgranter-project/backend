<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Access\Access;
use WishgranterProject\Backend\Access\AccessResultInterface;
use WishgranterProject\Backend\Access\AccessResultDeniedInterface;
use WishgranterProject\Backend\Access\AccessResultForbidden;
use WishgranterProject\Backend\Access\AccessResultUnauthorized;

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
        return new (get_called_class());
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

    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        return $this->accessGranted();
    }

    /**
     * Generates an access denied response.
     *
     * @param WishgranterProject\Backend\Access\AccessDeniedInterface $accessResult
     *   The computed access result.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The access denied response object.
     */
    public function deniedResponse(AccessResultDeniedInterface $accessResult): ResponseInterface
    {
        if ($accessResult instanceof AccessResultForbidden) {
            $statusCode = 403;
            $title = 'Unauthenticated';
        } else {
            $statusCode = 401;
            $title = 'Unauthorized';
        }

        $json = $this->jsonResource(null, $statusCode);
        $json->addError($statusCode, $title, $accessResult->getReason());
        return $json->renderResponse();
    }

    /**
     * Instantiates an access granted result object.
     *
     * @return WishgranterProject\Backend\Access\AccessResultInterface
     *   Access result.
     */
    protected function accessGranted(): AccessResultInterface
    {
        return Access::granted();
    }

    /**
     * Instantiates an access denied result object.
     *
     * @return WishgranterProject\Backend\Access\AccessResultInterface
     *   Access result.
     */
    protected function accessForbidden(string $reason = 'User unauthenticated'): AccessResultInterface
    {
        return Access::forbidden($reason);
    }

    /**
     * Instantiates an access denied result object.
     *
     * @return WishgranterProject\Backend\Access\AccessResultInterface
     *   Access result.
     */
    protected function accessUnauthorized(
        string $reason = 'Why unauthorized ? Who knows, lazy bastard never specified.'
    ): AccessResultInterface {
        return Access::unauthorized($reason);
    }
}
