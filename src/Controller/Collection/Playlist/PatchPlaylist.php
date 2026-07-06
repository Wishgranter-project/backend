<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use WishgranterProject\DescriptivePlaylist\Header;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Patches a playlist.
 */
class PatchPlaylist extends UpdatePlaylist
{
    /**
     * {@inheritdoc}
     */
    protected function getHeader(Playlist $playlist): Header
    {
        return $playlist->getHeader();
    }
}
