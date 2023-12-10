<?php
namespace AdinanCenci\Player\Service;

use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\DescriptivePlaylist\PlaylistItem;
use AdinanCenci\Player\Discography\Artist;
use AdinanCenci\Player\Discography\Release;
use AdinanCenci\AetherMusic\Resource\Resource;

class Describer 
{
    public static function create() : Describer
    {
        return new self();
    }

    public function describe($object) 
    {
        if ($object instanceof Playlist) {
            return $this->describePlaylist($object);
        } else if ($object instanceof PlaylistItem) {
            return $this->describePlaylistItem($object);
        } else if ($object instanceof Resource) {
            return $this->describeResource($object);
        } else if ($object instanceof Artist) {
            return $this->describeArtist($object);
        } else if ($object instanceof Release) {
            return $this->describeRelease($object);
        }
    }

    protected function describePlaylist($playlist) 
    {
        $data = [
            'type' => 'playlist',
            'id' => basename($playlist->fileName, '.dpls'),
        ];

        $data += (array) $playlist->getHeader()->getCopyOfTheData();
        return $data;
    }

    protected function describePlaylistItem(PlaylistItem $playlistItem) 
    {
        return $playlistItem->getCopyOfTheData();
    }

    protected function describeResource(Resource $resource) 
    {
        return ['type' => 'resource'] + $resource->toArray();
    }

    protected function describeArtist(Artist $artist) 
    {
        return ['type' => 'artist'] + $artist->toArray();
    }

    protected function describeRelease(Release $release) 
    {
        return ['type' => 'release'] + $release->toArray();
    }
}
