<?php 
namespace AdinanCenci\Player\Controller\Collection\Item;

use AdinanCenci\DescriptivePlaylist\Playlist;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class Get extends \AdinanCenci\Player\Controller\Collection\Playlist\Item\Get 
{
    protected function getObject($request, &$cacheHit = false) 
    {
        $cacheHit  = false;
        $itemId    = $request->getAttribute('item');
        $cacheKeys = ["item:$itemId"];

        if ($this->cacheManager->has($cacheKeys)) {
            $cacheHit = true;
            return $this->cacheManager->get($cacheKeys);
        }

        $playlists = $this->playlistManager->getPlaylistsIds();

        $object = [];
        foreach ($playlists as $playlistName) {
            if ($item = $this->searchPlaylist($playlistName, $itemId)) {
                $item->xxxPlaylist = $playlistName;
                $object['item'] = $item->getData();
                $this->cacheManager->set($cacheKeys, $object);
                return $object;
            }
        }

        throw new NotFound('Item ' . $itemId . ' not found.');
    }

    protected function searchPlaylist($playlistName, $itemId) 
    {
        $file = $this->playlistManager->file($playlistName);
        $playlist = new Playlist($file);

        return $playlist->getItemByUuid($itemId);
    }
}