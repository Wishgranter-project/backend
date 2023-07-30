<?php
namespace AdinanCenci\Player\Service;

use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\DescriptivePlaylist\PlaylistItem;

class Describer 
{
    public static function create() 
    {
        return new self();
    }

    public function describe($object) 
    {
        if ($object instanceof Playlist) {
            return $this->describePlaylist($object);
        } else if ($object instanceof PlaylistItem) {
            return $this->describePlaylistItem($object);
        }
    }

    protected function describePlaylist($playlist) 
    {
        $data = [
            'type' => 'playlist',
            'id' => basename($playlist->fileName, '.dpls'),
        ];

        $data += (array) $playlist->getHeader()->getData();
        return $data;
    }

    protected function describePlaylistItem($playlistItem) 
    {
        return $playlistItem->getData();
    }
}
