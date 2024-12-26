<?php

namespace WishgranterProject\Backend\Collection\Playlist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Deletes a playlist an all its items.
 */
class PlaylistDelete extends ControllerBase
{
    /**
     * The playlist manager.
     *
     * @var WishgranterProject\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
     *   The playlist manager.
     */
    public function __construct(PlaylistManager $playlistManager)
    {
        $this->playlistManager = $playlistManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new static($servicesManager->get('playlistManager'));
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $playlistId = $request->getAttribute('playlist');

        if (! $this->playlistManager->playlistExists($playlistId)) {
            throw new NotFound('Playlist ' . $playlistId . ' does not exist.');
        }

        $this->playlistManager->deletePlaylist($playlistId);

        $resource = new JsonResource();
        return $resource
            ->addSuccess(200, 'Playlist deleted')
            ->renderResponse();
    }
}
