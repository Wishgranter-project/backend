<?php

namespace WishgranterProject\Backend\Controller\Discover;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Service\Discography;
use WishgranterProject\Backend\Service\ServicesManager;

/**
 * Searches for artists by name.
 */
class DiscoverArtists extends AuthenticatedController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\Service\Discography $discography
     *   The discography service.
     * @param WishgranterProject\Backend\Service\Describer $describer
     *   The describer service.
     */
    public function __construct(
        protected AuthenticationInterface $authentication,
        protected Discography $discography,
        protected Describer $describer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        $called = get_called_class();
        return new $called(
            $servicesManager->get('authentication'),
            $servicesManager->get('discography'),
            $servicesManager->get('describer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->needsAnUser($request);

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
