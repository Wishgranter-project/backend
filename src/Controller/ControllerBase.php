<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Access\Access;
use WishgranterProject\Backend\Access\AccessResultInterface;
use WishgranterProject\Backend\Access\AccessResultBarredInterface;
use WishgranterProject\Backend\Access\AccessResultUnauthenticated;
use WishgranterProject\Backend\Access\AccessResultDenied;

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
     * @param WishgranterProject\Backend\Service\ServiceLocator $serviceManager
     *   The service manager for dependency injection.
     *
     * @return WishgranterProject\Backend\Controller\ControllerBase
     *   The instantiated controller.
     */
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
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
        return $handler->responseFactory->ok('Something went wrong.');
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

    /**
     * Given a request, checks if it is clear for a proper response.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\Backend\Access\AccessResultInterface
     *   Access result.
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        return $this->accessGranted();
    }

    /**
     * Generates an access denied response.
     *
     * @param WishgranterProject\Backend\Access\AccessDeniedInterface $accessDenied
     *   The computed access result.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The access denied response object.
     */
    public function deniedResponse(AccessResultBarredInterface $accessDenied): ResponseInterface
    {
        if ($accessDenied instanceof AccessResultUnauthenticated) {
            $statusCode = 401;
            $title = 'User unauthenticated';
        } else {
            $statusCode = 403;
            $title = 'Access denied';
        }

        $json = $this->jsonResource(null, $statusCode);
        $json->addError($statusCode, $title, $accessDenied->getReason());
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
     * Instantiates an access unauthenticated result object.
     *
     * @return WishgranterProject\Backend\Access\AccessResultInterface
     *   Access result.
     */
    protected function accessUnauthenticated(
        string $reason = 'User unauthenticated.'
    ): AccessResultInterface {
        return Access::unauthenticated($reason);
    }

    /**
     * Instantiates an access denied result object.
     *
     * @return WishgranterProject\Backend\Access\AccessResultInterface
     *   Access result.
     */
    protected function accessDenied(
        string $reason = 'You lack an unespecified permission.'
    ): AccessResultInterface {
        return Access::denied($reason);
    }
}
