<?php

namespace WishgranterProject\Backend\Controller\Discover;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;

/**
 * Fetches information about a release.
 */
class ReleaseRead extends AuthenticatedController
{
    /**
     * The playlist manager.
     *
     * @var WishgranterProject\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * The describer service.
     *
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * Constructor.
     *
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
     *   The playlist manager.
     * @param WishgranterProject\Backend\Service\Describer $describer
     *   The describer service.
     */
    public function __construct(PlaylistManager $playlistManager, Describer $describer)
    {
        $this->playlistManager = $playlistManager;
        $this->describer       = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new static(
            $servicesManager->get('playlistManager'),
            $servicesManager->get('describer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->needsAnUser($request);

        $release = $this->getRelease($request);

        $data = $this->describer->describe($release);

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
