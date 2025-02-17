<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;
use WishgranterProject\Discography\Artist;
use WishgranterProject\Discography\Album;
use WishgranterProject\AetherMusic\Resource\Resource;
use WishgranterProject\AetherMusic\Description;

class Describer
{
    public static function create(): Describer
    {
        return new self();
    }

    public function describe($object)
    {
        if ($object instanceof Playlist) {
            return $this->describePlaylist($object);
        } elseif ($object instanceof PlaylistItem) {
            return $this->describePlaylistItem($object);
        } elseif ($object instanceof Resource) {
            return $this->describeResource($object);
        } elseif ($object instanceof Description) {
            return $this->describeDescription($object);
        } elseif ($object instanceof Artist) {
            return $this->describeArtist($object);
        } elseif ($object instanceof Album) {
            return $this->describeAlbum($object);
        }
    }

    public function describeAll(array $array)
    {
        $described = [];

        foreach ($array as $k => $v) {
            $described[] = $this->describe($v);
        }

        return $described;
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

    protected function describeDescription(Description $description)
    {
        return ['type' => 'description'] + $description->toArray();
    }

    protected function describeArtist(Artist $artist)
    {
        return ['type' => 'artist'] + $artist->toArray();
    }

    protected function describeAlbum(Album $album)
    {
        return ['type' => 'album'] + $album->toArray();
    }
}
