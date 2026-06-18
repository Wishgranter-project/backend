<?php

namespace WishgranterProject\Backend\Controller\Collection;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\Service\CollectionManager;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;
use WishgranterProject\Backend\Access\AccessResultInterface;

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
     */
    public function __construct(
        protected AuthenticationInterface $authentication,
        protected CollectionManager $collectionManager
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
    {
        return new (get_called_class())(
            $serviceLocator->get('authentication'),
            $serviceLocator->get('collectionManager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $user = $this->getUser($request);
        if (!$user) {
            return $this->accessUnauthenticated();
        }

        $owner = $request->getAttribute('userName');

        // Rather basic logic, can be spanded if needed.
        return $owner == $user->getUsername()
            ? $this->accessGranted()
            : $this->accessUnauthorized('You are not allowed to access this user\'s collection');
    }

    /**
     * Returns the collection of the user's referenced in the request.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return null|WishgranterProject\DescriptiveManager\PlaylistManager
     *   The user's
     */
    public function getCollection(ServerRequestInterface $request): ?PlaylistManager
    {
        $user = $this->getUser($request);
        return $user
            ? $this->collectionManager->getCollection($user)
            : null;
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
