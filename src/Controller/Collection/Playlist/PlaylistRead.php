<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\DescriptivePlaylist\Playlist;

class PlaylistRead extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $playlistId = $request->getAttribute('playlist');

        if (! $this->playlistManager->playlistExists($playlistId)) {
            throw new NotFound('Playlist ' . $playlistId . ' does not exist.');
        }

        $playlist = $this->playlistManager->getPlaylist($playlistId);

        $aaa = $request->getHeaderLine('accept');

        return $request->getHeaderLine('accept') == 'application/jsonl'
            ? $this->download($request, $handler, $playlist)
            : $this->read($request, $handler, $playlist);
    }

    /**
     * Returns JSON data on the playlist.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     * @param WishgranterProject\DescriptivePlaylist\Playlist $playlist
     *   The playlist object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    protected function read(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        Playlist $playlist
    ): ResponseInterface {
        $data = $this->describer->describe($playlist);

        return $this->jsonResource($data)
            ->renderResponse();
    }

    /**
     * Downloads the playlist.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     * @param WishgranterProject\DescriptivePlaylist\Playlist $playlist
     *   The playlist object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    protected function download(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        Playlist $playlist
    ): ResponseInterface {
        $file     = $playlist->fileName;
        $basename = basename($file);

        $response = $handler->responseFactory->ok(file_get_contents($file));
        $response = $response->withAddedHeader('content-type', 'application/jsonl');
        $response = $response->withAddedHeader('Content-Disposition', "attachment; filename=\"$basename\"");

        return $response;
    }
}
