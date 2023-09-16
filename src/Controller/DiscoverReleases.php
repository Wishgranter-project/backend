<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class DiscoverReleases extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $searchResults = $this->listReleases($request);
        $resource = $searchResults->getJsonResource();
        return $resource->renderResponse();
    }

    protected function listReleases($request) 
    {
        $name = $request->get('artist');
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        $page = (int) $request->get('page', 1);

        return $this->discographyFinder->searchForReleasesByArtistName($name, $page);
    }
}
