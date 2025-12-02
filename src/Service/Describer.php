<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;
use WishgranterProject\Discography\Artist;
use WishgranterProject\Discography\Album;
use WishgranterProject\AetherMusic\Resource\Resource;
use WishgranterProject\AetherMusic\Description;

/**
 * Service to generate flat associative arrays out of different objects.
 *
 * @todo Replace this with something better.
 */
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
        }

        if ($object instanceof PlaylistItem) {
            return $this->describePlaylistItem($object);
        }

        if ($object instanceof Resource) {
            return $this->toArray($object, 'resource');
        }

        if ($object instanceof Description) {
            return $this->toArray($object, 'description');
        }

        if ($object instanceof Artist) {
            return $this->toArray($object, 'artist');
        }

        if ($object instanceof Album) {
            return $this->toArray($object, 'album');
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
        $data = (array) $playlist->getHeader()->getCopyOfTheData();

        $data += [
            'type' => 'playlist',
            'id' => basename($playlist->fileName, '.dpls'),
        ];

        return $data;
    }

    protected function describePlaylistItem(PlaylistItem $playlistItem)
    {
        return (array) $playlistItem->getCopyOfTheData();
    }

    protected function toArray($object, $type): array
    {
        return ['type' => $type] + $object->toArray();
    }
}
