<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Discography\Source\SourceMusicBrainz;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Helper\JsonResource;

class DiscoverAlbums extends ControllerBase
{
    /**
     * @var WishgranterProject\Discography\Source\SourceMusicBrainz
     */
    protected SourceMusicBrainz $discography;

    /**
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param WishgranterProject\Discography\Source\SourceMusicBrainz $discography
     * @param WishgranterProject\Backend\Service\Describer $describer
     */
    public function __construct(SourceMusicBrainz $discography, Describer $describer)
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
            $servicesManager->get('discographyMusicBrainz'),
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
        $searchResults = $this->listReleases($request);
        $resource      = new JsonResource();
        $data          = $this->describer->describeAll($searchResults);

        $resource->setData($data);
        return $resource->renderResponse();
    }

    protected function listReleases(ServerRequestInterface $request)
    {
        $artistName = $request->get('artist');

        if (empty($artistName) || !is_string($artistName)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        return $this->discography->getArtistsAlbums($artistName);
    }
}
