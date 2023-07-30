<?php 
namespace AdinanCenci\Player\Controller\Collection\Item;

use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\Player\Exception\NotFound;

class Sources extends \AdinanCenci\Player\Controller\Collection\Playlist\Item\Sources 
{
    protected function getItem($request) 
    {
        $itemId = $request->getAttribute('item');

        $playlists = $this->playlistManager->getPlaylists();
        foreach ($playlists as $playlist) {
            if ($item = $playlist->getItemByUuid($itemId)) {
                return $item;
            }
        }

        throw new NotFound('Item ' . $itemId . ' not found.');
    }
}
