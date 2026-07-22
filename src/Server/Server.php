<?php

namespace WishgranterProject\Backend\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use AdinanCenci\Router\Caller\Caller as DefaultCaller;
use AdinanCenci\Router\Router;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Exception\Unauthorized;
use WishgranterProject\Backend\Service\ServiceLocator;
use AdinanCenci\Router\Caller\Handler\ObjectAndMethodHandler;

final class Server
{
    public function __construct(protected $serviceLocator)
    {
    }

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
        $caller = $this->getCaller();
        $router = new Router(null, null, null, $caller);
        $router->setNotFoundHandler([$this, 'handleNotFoundError']);
        $router->setExceptionHandler([$this, 'handleException']);

        require $routes;

        return $router;
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

    /**
     * Returns our caller.
     *
     * The object that will invoke our routes callbacks.
     *
     * @return AdinanCenci\Router\Caller\CallerInterface
     *   The caller.
     */
    protected function getCaller()
    {
        $instantiator = new CustomControllerInstantiator($this->serviceLocator);

        $handlers = [
            new CustomClassHandler($instantiator),
            //new ClousureHandler(),
            //new FileHandler(),
            //new FunctionHandler(),
            //new MethodHandler($instantiator),
            new ObjectAndMethodHandler(),
            //new ObjectHandler(),
            //new StaticMethodHandler(),
        ];

        return new DefaultCaller($handlers);
    }
}
