<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\Service\Discography;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Helper\JsonResource;

class DiscoverArtists extends ControllerBase
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
        $searchResults = $this->searchArtists($request);
        $array         = $this->describer->describeAll($searchResults);
        $resource      = new JsonResource($array);
        return $resource->renderResponse();
    }

    protected function searchArtists(ServerRequestInterface $request): array
    {
        $name = $request->get('name');

        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        return $this->discography->searchForArtist($name);
    }
}
