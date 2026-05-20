<?php

namespace WishgranterProject\Backend\Controller\Discover;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Searches for an album by its artist and title.
 */
class DiscoverAlbum extends DiscoverArtists
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $artistName = $request->get('artist');
        $albumTitle = $request->get('title');
        $album      = $this->discography->getAlbum($artistName, $albumTitle);

        if (!$album) {
            $resource = $this->jsonResource($data, 404);
            $resource->addError(404, 'No results', 'Nothing found');
            return $resource->renderResponse();
        }

        $data       = $album->toArray();
        return $this->jsonResource($data)
            ->renderResponse();
    }
}
