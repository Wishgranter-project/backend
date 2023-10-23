<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class ArtistsList extends ControllerBase 
{
    protected array $artists = [];

    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $this->listArtists($request);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($this->artists)
            ->renderResponse();
    }

    protected function listArtists($request) 
    {
        $artists = [];
        foreach ($this->playlistManager->getAllPlaylists() as $playlistId => $playlist) {
            foreach ($playlist->items as $key => $item) {
                if (! $item->artist) {
                    continue;
                }
                $this->countArtists((array) $item->artist);
            }
        }

        krsort($this->artists);
        arsort($this->artists);
    }

    protected function countArtists(array $artists) 
    {
        foreach ($artists as $a) {
            if (!isset($this->artists[$a])) {
                $this->artists[$a] = 1;
            } else {
                $this->artists[$a]++;
            }
        }
    }
}
