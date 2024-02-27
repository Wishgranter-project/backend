<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;

class PlaylistDelete extends ControllerBase
{
    /**
     * @var WishgranterProject\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
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
    public function generateResponse(
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
