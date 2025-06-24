<?php

namespace WishgranterProject\Backend\Collection\Playlist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Updates a playlist.
 */
class PlaylistUpdate extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $playlistId = $request->getAttribute('playlist');

        $playlist = $this->playlistManager->getPlaylist($playlistId);
        if (! $playlist) {
            throw new NotFound('Playlist ' . $playlistName . ' not found.');
        }

        $header   = $playlist->getHeader();
        $header->empty();

        $postData = $this->getPostData($request);

        foreach ($postData as $k => $v) {
            if ($header->isValidPropertyName($k)) {
                $header->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }

        $playlist->setHeader($header);

        $data = $this->describer->describe($playlist);

        return $this->jsonResource($data)
            ->addSuccess(200, 'Changes saved')
            ->renderResponse();
    }
}
