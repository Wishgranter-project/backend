<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Discography\AlbumInterface;
use WishgranterProject\Backend\Helper\Singleton;

/**
 * Service to search for discography information.
 */
class Discography extends Singleton
{
    /**
     * Sources of discographic information.
     *
     * @var WishgranterProject\Discography\SourceInterface[]
     */
    protected array $sources = [];

    /**
     * Constructor.
     *
     * @param WishgranterProject\Discography\SourceInterface[] $sources
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
     * @return \WishgranterProject\Discography\ArtistInterface[]
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
     * @return \WishgranterProject\Discography\AlbumInterface[]
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
     * @return \WishgranterProject\Discography\AlbumInterface|null
     *   The album we are looking for.
     */
    public function getAlbum(string $artistName, string $title): ?AlbumInterface
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
