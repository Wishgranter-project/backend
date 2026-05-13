<?php

namespace WishgranterProject\Backend\Controller\Discover;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;

/**
 * Fetches information about a release.
 */
class ReleaseRead extends AuthenticatedController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
     *   The playlist manager.
     */
    public function __construct(protected PlaylistManager $playlistManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new static(
            $servicesManager->get('playlistManager'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $release = $this->getRelease($request);

        $data = $release->toArray();

        return $this->jsonResource($data)
            ->renderResponse();
    }

    /**
     * Get the album.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\Discography\Album
     *   The album object.
     */
    protected function getRelease(ServerRequestInterface $request)
    {
        $releaseId = $request->getAttribute('releaseId');
        return $this->discographyDiscogs->getAlbumById($releaseId);
    }
}
