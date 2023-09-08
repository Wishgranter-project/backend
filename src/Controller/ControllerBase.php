<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Exception\NotFound;

class ControllerBase 
{
    protected ServicesManager $servicesManager;

    public function __construct() 
    {
        $this->servicesManager = ServicesManager::singleton();
    }

    public function __get($var) {
        return $this->servicesManager->get($var);
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        try {
            $response = $this->formResponse($request, $handler);
        } catch(\Exception $e) {
            $response = $this->handleException($e);
        }

        if (! $response instanceof ResponseInterface) {
            $response = $this->handleException(new \Exception('Controller failed to return a response'));
        }

        return $response;
    }

    protected function handleException(\Exception $exception) : ResponseInterface
    {
        $resource = new JsonResource();

        if ($exception instanceof NotFound) {
            $resource
                ->setStatusCode(404)
                ->addError(404, $exception->getMessage());
        } else if ($exception instanceof \InvalidArgumentException) {
            $resource
                ->setStatusCode(400)
                ->addError(400, $exception->getMessage());
        } else {
            $resource
                ->setStatusCode(500)
                ->addError(500, $exception->getMessage());
        }

        return $resource->renderResponse();
    }

    protected function getPostData(ServerRequestInterface $request) : array
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

    protected static function getMime(string $contentType) : string
    {
        return preg_match('#^([^;]+)#', $contentType, $matches)
            ? trim(strtolower($matches[1]))
            : $contentType;
    }
}
