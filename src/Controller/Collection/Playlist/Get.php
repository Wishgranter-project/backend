<?php 
namespace AdinanCenci\Player\Controller\Collection\Playlist;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\DescriptivePlaylist\Playlist;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class Get extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $playlistId = $request->getAttribute('playlist');
        $playlist = $this->playlistManager->getPlaylist($playlistId);

        if (! $playlist) {
            throw new NotFound('Playlist ' . $playlistId . ' not found.');
        }

        $resource = new JsonResource();
        return $resource
            ->code(200)
            ->playlist($playlist->getHeader()->getData())
            ->getResponse();
    }
}
