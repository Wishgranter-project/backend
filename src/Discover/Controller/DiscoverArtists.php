<?php

namespace WishgranterProject\Backend\Discover\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\Service\Discography;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;

/**
 * Searches for artists by name.
 */
class DiscoverArtists extends ControllerBase
{
    /**
     * The discography service.
     *
     * @var WishgranterProject\Backend\Service\Discography
     */
    protected Discography $discography;

    /**
     * The describer service.
     *
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Service\Discography $discography
     *   The discography service.
     * @param WishgranterProject\Backend\Service\Describer $describer
     *   The describer service.
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
        $called = get_called_class();
        return new $called(
            $servicesManager->get('discography'),
            $servicesManager->get('describer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $artistName = $request->get('name');

        if (empty($artistName) || !is_string($artistName)) {
            throw new \InvalidArgumentException('Provide the name of an artist or band.');
        }

        $searchResults = $this->discography->searchForArtist($artistName);
        $array         = $this->describer->describeAll($searchResults);

        return $this->jsonResource($array)
            ->renderResponse();
    }
}
