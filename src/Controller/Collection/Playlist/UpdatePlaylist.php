<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\DescriptivePlaylist\Header;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Updates a playlist.
 */
class UpdatePlaylist extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);

        $playlistId = $request->getAttribute('playlist');

        $playlist = $collection->getPlaylist($playlistId);
        if (! $playlist) {
            throw new NotFound('Playlist ' . $playlistName . ' not found.');
        }

        $header = $this->getHeader($playlist);

        $postData = $this->getPostData($request);
        foreach ($postData as $k => $v) {
            if ($header->isValidPropertyName($k)) {
                $header->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }

        $playlist->setHeader($header);

        $data = $this->dataTransferPlaylist($playlist);

        return $this->jsonResource($data)
            ->addSuccess(200, 'Changes saved')
            ->renderResponse();
    }

    /**
     * Retrieves a playlist's header.
     *
     * @param WishgranterProject\DescriptivePlaylist\Playlist $playlist
     *   Playlist object.
     *
     * @return WishgranterProject\DescriptivePlaylist\Header
     *   Header object.
     */
    protected function getHeader(Playlist $playlist): Header
    {
        $header = $playlist->getHeader();
        $header->empty();

        return $header;
    }
}
