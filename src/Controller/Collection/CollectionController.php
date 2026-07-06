<?php

namespace WishgranterProject\Backend\Controller\Collection;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\AuthenticationManagerInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\Service\CollectionManager;
use WishgranterProject\Backend\User\UserManager;
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
     * @param WishgranterProject\Backend\Authentication\AuthenticationManagerInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\User\UserManager $userManager
     *   User manager service.
     * @param WishgranterProject\Backend\Service\CollectionManager $collectionManager
     *   Collection manager service.
     */
    public function __construct(
        protected AuthenticationManagerInterface $authentication,
        protected UserManager $userManager,
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
            $serviceLocator->get('userManager'),
            $serviceLocator->get('collectionManager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAccess(ServerRequestInterface $request): AccessResultInterface
    {
        $authenticatedUser = $this->getAuthenticatedUser($request);
        if (!$authenticatedUser) {
            return $this->accessUnauthenticated();
        }

        $isTheOwner = $authenticatedUser->getId() == $request->getAttribute('userId');
        $isAdmin = $authenticatedUser->hasRole('admin');

        return $isTheOwner || $isAdmin
            ? $this->accessGranted()
            : $this->accessDenied('You are not allowed to access this user\'s collection.');
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
        $userId = $request->getAttribute('userId');
        if (!$this->userManager->userExists($userId)) {
            return null;
        }

        $user = $this->userManager->getUser($userId);
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
