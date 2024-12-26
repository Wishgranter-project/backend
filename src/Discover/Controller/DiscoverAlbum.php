<?php

namespace WishgranterProject\Backend\Discover\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Helper\JsonResource;

class DiscoverAlbum extends DiscoverArtists
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $artistName = $request->get('artist');
        $albumTitle = $request->get('title');
        $album      = $this->discography->getAlbum($artistName, $albumTitle);
        $data       = $this->describer->describe($album);

        $resource = new JsonResource($data, 200);

        return $resource->renderResponse();
    }
}
