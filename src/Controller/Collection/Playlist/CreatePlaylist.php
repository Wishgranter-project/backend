<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Creates a new playlist.
 */
class CreatePlaylist extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);

        $title = (string) $request->post('title');
        if (empty($title)) {
            throw new \InvalidArgumentException('Inform a valid title for the playlist.');
        }

        $playlist = $collection->createPlaylist($title, null, $title, $playlistId);
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
            $collection->deletePlaylist($playlistId);
            throw $e;
        }

        $data = $this->dataTransferPlaylist($playlist);

        return $this->jsonResource($data, 201)
            ->addSuccess(201, 'Playlist created')
            ->renderResponse();
    }
}
