<?php

namespace WishgranterProject\Backend;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use AdinanCenci\Router\Caller\Caller as DefaultCaller;
use AdinanCenci\Router\Router;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Exception\Unauthorized;
use WishgranterProject\Backend\Service\ServicesManager;

final class Server
{
    /**
     * Returns the router object.
     *
     * @param string $routes
     *   The file containing the defined routes.
     *
     * @return AdinanCenci\Router\Router
     *   The PSR-15 compliant router to handle requests and responses.
     */
    public function getRouter(string $routes): Router
    {
        $instantiator = new ControllerInstantiator(ServicesManager::singleton());
        $caller       = DefaultCaller::withDefaultHandlers($instantiator);
        $router       = new Router(null, null, null, $caller);
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
     * Cors pre-flight controller for CORS.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response for the request.
     */
    public function preFlight(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->responseFactory->ok('');
        $response = $this->withAddedCorsHeaders($request, $response);

        return $response;
    }

    /**
     * Generates a response and adds CORS headers.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response for the request.
     */
    public function decorator(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $response = $handler->handle($request);
        if (!$response->hasHeader('Access-Control-Allow-Origin')) {
            $response = $this->withAddedCorsHeaders($request, $response);
        }
        return $response;
    }

    /**
     * Returns a response with CORS headers added.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\ResponseInterface $response
     *   The response for the request.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response for the request with CORS headers.
     */
    public function withAddedCorsHeaders(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $response = $response->withAddedHeader('Access-Control-Allow-Origin', getCorsAllowedDomain($request, $GLOBALS['settings']));
        $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
        $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'content-type');
        $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        return $response;
    }

    /**
     * Method called when no mathing router is found for the request.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     * @param string $path
     *   The path of the request.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response for the request.
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
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     * @param string $path
     *   The path of the request.
     * @param Exception $exception
     *   The exception to handle.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response for the request.
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
        } elseif ($exception instanceof Unauthorized) {
            $resource
                ->setStatusCode(403)
                ->addError(403, $exception->getMessage());
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
