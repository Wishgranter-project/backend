<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class ArtistsList extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $data = $this->listArtists($request);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($data)
            ->renderResponse();
    }

    public function listArtists($request) 
    {
        $artists = [];
        foreach ($this->playlistManager->getAllPlaylists() as $playlistId => $playlist) {
            foreach ($playlist->items as $key => $item) {
                if (! $item->artist) {
                    continue;
                }
                $artists = array_merge($artists, (array) $item->artist);
            }
        }

        $artists = array_values(array_unique($artists));
        sort($artists);

        return $artists;
    }
}
