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
        $this->needsAnUser($request);

        $artistName = $request->get('artist');
        if (empty($artistName) || !is_string($artistName)) {
            throw new \InvalidArgumentException('Provide the name of an artist or band.');
        }

        $searchResults = $this->discography->getArtistsAlbums($artistName);
        $array         = $this->describer->describeAll($searchResults);

        return $this->jsonResource($array)
            ->renderResponse();
    }
}
