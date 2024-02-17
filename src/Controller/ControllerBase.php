<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Exception\NotFound;

abstract class ControllerBase
{
    /**
     * @param AdinanCenci\Player\Service\ServicesManager $serviceManager
     *   The service manager.
     *
     * @return AdinanCenci\Player\Controller\ControllerBase
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new (\get_called_class());
    }

    /**
     * Instantiate and invoke the controler.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public static function respond(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $servicesManager = ServicesManager::singleton();
        $controller = get_called_class()::instantiate($servicesManager);
        return $controller($request, $handler);
    }

    /**
     * Invoke the controler to generate a response.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->generateResponse($request, $handler);

        if (! $response instanceof ResponseInterface) {
            throw new \Exception('Controller failed to return a response');
        }

        return $response;
    }

    public function __construct()
    {
        // Controller specific.
    }

    /**
     * Generate a response.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    protected function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Controller specific.
    }

    /**
     * Just like $_POST.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *
     * @return array
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
     *
     * @return string
     */
    private static function getMime(string $contentType): string
    {
        return preg_match('#^([^;]+)#', $contentType, $matches)
            ? trim(strtolower($matches[1]))
            : $contentType;
    }
}
