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
        $resource = new JsonResource();
        $data = $this->describer->describeAll($searchResults);        

        $resource->setData($data);
        return $resource->renderResponse();
    }

    protected function listReleases($request) 
    {
        $artistName = $request->get('artist');
        if (empty($artistName) || !is_string($artistName)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        return $this->discographyMusicBrainz->getArtistsAlbums($artistName);
    }
}
