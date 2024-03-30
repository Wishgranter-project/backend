<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Discography\Album;

class Discography extends Singleton
{
    /**
     * [WishgranterProject\Discography\Source\SourceInterface]
     */
    protected array $sources = [];

    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function searchForArtist(string $artistName): array
    {
        foreach ($this->sources as $id => $source) {
            $results = $source->searchForArtist($artistName);
            if ($results) {
                return $results;
            }
        }

        return [];
    }

    public function getArtistsAlbums(string $artistName): array
    {
        foreach ($this->sources as $id => $source) {
            $albums = $source->getArtistsAlbums($artistName);
            if ($albums) {
                return $albums;
            }
        }

        return [];
    }

    public function getAlbum(string $artistName, string $title): ?Album
    {
        foreach ($this->sources as $id => $source) {
            $album = $source->getAlbum($artistName, $title);
            if ($album) {
                return $album;
            }
        }

        return null;
    }
}
