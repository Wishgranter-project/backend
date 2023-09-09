<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class DiscoverResources extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $resources = $this->find($request);

        $data = [];
        foreach ($resources as $resource) {
            $data[] = $this->describer->describe($resource);
        }

        $resource = new JsonResource();
        return $resource
            ->setData($data)
            ->renderResponse();

        return $response;
    }

    protected function find(ServerRequestInterface $request) 
    {
        $parameters = $this->buildParameters($request);
        $resources  = $this->resourceFinder->findResources($parameters);
        return $resources;
    }

    protected function buildParameters(ServerRequestInterface $request) : array
    {
        $parameters['title'] = $request->get('title');

        if ($request->get('artist')) {
            $parameters['artist'] = $request->get('artist');
        }

        if ($request->get('soundtrack')) {
            $parameters['soundtrack'] = $request->get('soundtrack');
        }

        return $parameters;
    }
}
