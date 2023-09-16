<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Helper\SearchResults;

class DiscoverArtists extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $searchResults = $this->searchArtists($request);
        $resource = $searchResults->getJsonResource();
        return $resource->renderResponse();
    }

    protected function searchArtists(ServerRequestInterface $request) : SearchResults
    {
        $name = $request->get('name');

        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        return $this->discographyFinder->searchForArtistByName($name);
    }
}
