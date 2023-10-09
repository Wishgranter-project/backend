<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\AetherMusic\Description;

use AdinanCenci\Player\Service\ServicesManager;
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

    protected function find(ServerRequestInterface $request) : array
    {
        $description = $this->buildDescription($request);
        $resources   = $this->aether->search($description);
        return $resources;
    }

    protected function buildDescription(ServerRequestInterface $request) : Description
    {
        return Description::createFromArray([
            'title'      => $request->get('title'),
            'artist'     => $request->get('artist'),
            'soundtrack' => $request->get('soundtrack')
        ]);
    }
}
