<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class ItemDelete extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $uuid = $request->getAttribute('itemUuid');
        if (!$item = $this->playlistManager->findItemByUuid($uuid, $playlistId)) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        $playlist = $this->playlistManager->getPlaylist($playlistId);
        $playlist->deleteItem($item);
        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->addSuccess(200, 'Item deleted')
            ->renderResponse();
    }
}
