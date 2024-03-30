<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Service\Discography;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Helper\JsonResource;

class DiscoverAlbum extends ControllerBase
{
    /**
     * @var WishgranterProject\Backend\Service\Discography
     */
    protected Discography $discography;

    /**
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param WishgranterProject\Backend\Service\Discography $discography
     * @param WishgranterProject\Backend\Service\Describer $describer
     */
    public function __construct(Discography $discography, Describer $describer)
    {
        $this->discography = $discography;
        $this->describer   = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new self(
            $servicesManager->get('discography'),
            $servicesManager->get('describer')
        );
    }

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
