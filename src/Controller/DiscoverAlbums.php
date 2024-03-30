<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Service\Discography;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Helper\JsonResource;

class DiscoverAlbums extends ControllerBase
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
        $searchResults = $this->listAlbums($request);
        $array         = $this->describer->describeAll($searchResults);
        $resource      = new JsonResource($array);
        return $resource->renderResponse();
    }

    protected function listAlbums(ServerRequestInterface $request)
    {
        $artistName = $request->get('artist');

        if (empty($artistName) || !is_string($artistName)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        return $this->discography->getArtistsAlbums($artistName);
    }
}
