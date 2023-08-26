<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\DescriptivePlaylist\PlaylistItem;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class ItemCreate extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $postData = $this->getPostData($request);
        if (empty($postData['playlist']) || !is_string($postData['playlist'])) {
            throw new \InvalidArgumentException('Inform a playlist');
        }
        $playlistId = $postData['playlist'];
        unset($postData['playlist']);

        if (!$playlist = $this->playlistManager->getPlaylist($playlistId)) {
            throw new \InvalidArgumentException('Playlist ' . $playlistId . ' does not exist');
        }

        $item = new PlaylistItem();
        foreach ($postData as $k => $v) {
            if ($item->isValidPropertyName($k)) {
                $item->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }
        $item->clear();
        $playlist->setItem($item);

        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(201)
            ->addSuccess(201, 'Item created')
            ->setData($data)
            ->renderResponse();
    }
}
