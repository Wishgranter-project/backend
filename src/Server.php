<?php

namespace WishgranterProject\Backend;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use AdinanCenci\Router\Router;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Exception\NotFound;

final class Server
{
    /**
     * @param string $routes
     *   The file containing the defined routes.
     *
     * @return AdinanCenci\Router\Router
     */
    public function getRouter(string $routes): Router
    {
        $router = new Router();
        $router->setNotFoundHandler([$this, 'handleNotFoundError']);
        $router->setExceptionHandler([$this, 'handleException']);
        // CORS Pre-flight.
        $router->add('OPTIONS', '#^.*$#', [$this, 'preFlight']);
        // Add CORS headers to all responses.
        $router->before('GET|POST|PUT|PATCH|DELETE', '#^.*$#', [$this, 'decorator']);

        require $routes;

        return $router;
    }

    /**
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function preFlight(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->responseFactory->ok('');
        $response = $response->withAddedHeader('Access-Control-Allow-Origin', getCorsAllowedDomain($request, $GLOBALS['settings']));
        $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
        $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'content-type');
        $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');

        return $response;
    }

    /**
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function decorator(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $response = $handler->handle($request);
        if (!$response->hasHeader('Access-Control-Allow-Origin')) {
            $response = $response->withAddedHeader('Access-Control-Allow-Origin', getCorsAllowedDomain($request, $GLOBALS['settings']));
            $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
            $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'content-type');
            $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        }
        return $response;
    }

    /**
     * Method to handle not found errors.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     * @param string $path
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function handleNotFoundError(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        string $path
    ): ResponseInterface {

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(404)
            ->addError(404, 'nothing found related to ' . $path)
            ->renderResponse();
    }

    /**
     * Method to handle exceptions.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     * @param string $path
     * @param Exception $exception
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function handleException(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        string $path,
        \Exception $exception
    ): ResponseInterface {

        $resource = new JsonResource();

        if ($exception instanceof NotFound) {
            $resource
                ->setStatusCode(404)
                ->addError(404, $exception->getMessage());
        } elseif ($exception instanceof \InvalidArgumentException) {
            $resource
                ->setStatusCode(400)
                ->addError(400, $exception->getMessage());
        } else {
            $resource
                ->setStatusCode(500)
                ->addError(500, $exception->getMessage());
        }

        return $resource
            ->renderResponse();
    }
}
