<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;
use WishgranterProject\Discography\Artist;

/**
 * Service to manage the collection of different users.
 */
class CollectionManager
{
    /**
     * Constructor.
     *
     * @param string $directory.
     *   The directory where the user collections are stored.
     */
    public function __construct(protected string $directory)
    {
    }

    /**
     * Returns the collection of a given user.
     *
     * @param WishgranterProject\Backend\User\UserInterface $user
     *   The user.
     *
     * @return WishgranterProject\DescriptiveManager\PlaylistManager
     *   The user's music collection.
     */
    public function getCollection(UserInterface $user): PlaylistManager
    {
        $directory = $this->directory . $user->getUsername() . '/';
        if (!file_exists($directory)) {
            mkdir($directory);
        }

        return new PlaylistManager($directory);
    }
}
