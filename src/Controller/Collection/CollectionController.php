<?php

namespace WishgranterProject\Backend\Controller\Collection;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\CollectionManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;

/**
 * Base collection controller.
 */
abstract class CollectionController extends AuthenticatedController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\Service\CollectionManager $collectionManager
     *   Collection manager service.
     * @param WishgranterProject\Backend\Service\Describer $describer
     *   The describer service.
     */
    public function __construct(
        protected AuthenticationInterface $authentication,
        protected CollectionManager $collectionManager,
        protected Describer $describer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new (get_called_class())(
            $servicesManager->get('authentication'),
            $servicesManager->get('collectionManager'),
            $servicesManager->get('describer')
        );
    }

    public function getCollection(ServerRequestInterface $request)
    {
        $user = $this->needsAnUser($request);
        return $this->collectionManager->getCollection($user);
    }

    /**
     * Generates a data transfer object out of a given playlist.
     *
     * @param WishgranterProject\DescriptivePlaylist\Playlist $playlist
     *   A playlist object.
     *
     * @return \stdClass
     *   Data for transfer.
     */
    protected function dataTransferPlaylist(Playlist $playlist): \stdClass
    {
        $data = $playlist->getHeader()->getCopyOfTheData();
        $data->id = basename($playlist->fileName, '.dpls');
        return $data;
    }

    /**
     * Generates a data transfer object out of a given playlist item.
     *
     * @param WishgranterProject\DescriptivePlaylist\PlaylistItem $playlistItem
     *   A playlist object.
     *
     * @return \stdClass
     *   Data for transfer.
     */
    protected function dataTransferItem($playlistItem): \stdClass
    {
        return $playlistItem->getCopyOfTheData();
    }
}
