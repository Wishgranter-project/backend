<?php

namespace WishgranterProject\Backend\Discover\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\Discography;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Service\ServicesManager;

class DiscoverAlbum extends DiscoverArtists
{
    /**
     * {@inheritdoc}
     */
    public function generateResponse(
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
