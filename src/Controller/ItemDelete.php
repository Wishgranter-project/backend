<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\DescriptivePlaylist\PlaylistItem;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class ItemDelete extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $playlistId = $request->getAttribute('playlist');
        if (!$playlist = $this->playlistManager->getPlaylist($playlistId)) {
            throw new NotFound('Playlist ' . $playlistId . ' does not exist.');
        }

        $uuid = $request->getAttribute('itemUuid');
        if (!$item = $playlist->getItemByUuid($uuid)) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        $playlist->deleteItem($item);
        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->addSuccess(200, 'Item deleted')
            ->renderResponse();
    }
}
