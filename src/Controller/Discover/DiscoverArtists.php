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
use WishgranterProject\Backend\Service\Discography;
use WishgranterProject\Backend\Service\ServiceLocator;

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
     */
    public function __construct(
        protected AuthenticationInterface $authentication,
        protected Discography $discography,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
    {
        $called = get_called_class();
        return new $called(
            $serviceLocator->get('authentication'),
            $serviceLocator->get('discography'),
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
        $data          = array_map([$this, 'dataTransferArtist'], $searchResults);

        return $this->jsonResource($data)
            ->renderResponse();
    }

    /**
     * Generates a data transfer object out of a given artist object.
     *
     * @param WishgranterProject\Discography\ArtistInterface $artist
     *   An artist object.
     *
     * @return array
     *   Data for transfer.
     */
    protected function dataTransferArtist($artist): array
    {
        return $artist->toArray();
    }
}
