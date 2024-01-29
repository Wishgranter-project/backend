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
        $resource = JsonResource::fromSearchResults($searchResults);
        return $resource->renderResponse();
    }

    protected function listReleases($request) 
    {
        $artistName = $request->get('artist');
        if (empty($artistName) || !is_string($artistName)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        $page = (int) $request->get('page', 1);
        return $this->discographyDiscogs->searchForAlbumsByArtistName($artistName, $page);
    }
}
