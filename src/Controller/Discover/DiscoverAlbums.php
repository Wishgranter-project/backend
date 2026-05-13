<?php

namespace WishgranterProject\Backend\Controller\Discover;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Searches for albums by their artist's names.
 */
class DiscoverAlbums extends DiscoverArtists
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $artistName = $request->get('artist');
        if (empty($artistName) || !is_string($artistName)) {
            throw new \InvalidArgumentException('Provide the name of an artist or band.');
        }

        $searchResults = $this->discography->getArtistsAlbums($artistName);
        $data          = array_map([$this, 'dataTransferAlbum'], $searchResults);

        return $this->jsonResource($data)
            ->renderResponse();
    }

    /**
     * Generates a data transfer object out of a given album object.
     *
     * @param WishgranterProject\Discography\AlbumInterface $album
     *   An album object.
     *
     * @return array
     *   Data for transfer.
     */
    protected function dataTransferAlbum($album): array
    {
        return $album->toArray();
    }
}
