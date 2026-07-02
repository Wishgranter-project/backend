<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Reads a individual playlist.
 */
class ReadPlaylist extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);

        $playlistId = $request->getAttribute('playlist');

        if (! $collection->playlistExists($playlistId)) {
            throw new NotFound('Playlist ' . $playlistId . ' does not exist.');
        }

        $playlist = $collection->getPlaylist($playlistId);
        $data = $this->dataTransferPlaylist($playlist);
        return $this->jsonResource($data)
            ->renderResponse();
    }
}
