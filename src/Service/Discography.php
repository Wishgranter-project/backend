<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Discography\Album;
use WishgranterProject\Backend\Helper\Singleton;

class Discography extends Singleton
{
    /**
     * @var WishgranterProject\Discography\Source\SourceInterface[]
     *   Sources of discographic information.
     */
    protected array $sources = [];

    /**
     * @param WishgranterProject\Discography\Source\SourceInterface[] $sources
     *   Sources of discographic information.
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    /**
     * Search for artists by name.
     *
     * @param string $artistName
     *   The name of the artist we are looking for.
     *
     * @return \WishgranterProject\Discography\Artist[]
     *   Array of matching artists.
     */
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

    /**
     * Search for albums by the artist's name.
     *
     * @param string $artistName
     *   The name of the artist we are looking for.
     *
     * @return \WishgranterProject\Discography\Albums[]
     *   Array of matching albums.
     */
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

    /**
     * Gets information on a particular album.
     *
     * @param string $artistName
     *   The name of the artist we are looking for.
     * @param string $title
     *   The title of the album.
     *
     * @return \WishgranterProject\Discography\Albums|null
     *   The album we are looking for.
     */
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
