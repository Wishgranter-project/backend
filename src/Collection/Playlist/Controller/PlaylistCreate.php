<?php

namespace WishgranterProject\Backend\Collection\Playlist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Creates a new playlist.
 */
class PlaylistCreate extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $title    = (string) $request->post('title');
        if (empty($title)) {
            throw new \InvalidArgumentException('Inform a valid title for the playlist.');
        }

        $playlist = $this->playlistManager->createPlaylist($title, null, $title, $playlistId);
        $header   = $playlist->getHeader();

        try {
            $postData = $this->getPostData($request);
            foreach ($postData as $key => $v) {
                if ($header->isValidPropertyName($key)) {
                    $header->{$key} = $v;
                } else {
                    throw new \InvalidArgumentException('Unrecognized property ' . $key);
                }
            }
            $playlist->setHeader($header);
        } catch (\Exception $e) {
            $this->playlistManager->deletePlaylist($playlistId);
            throw $e;
        }

        $resource = new JsonResource();
        $data = $this->describer->describe($playlist);

        return $resource
            ->setStatusCode(201)
            ->addSuccess(201, 'Playlist created')
            ->setData($data)
            ->renderResponse();
    }
}
