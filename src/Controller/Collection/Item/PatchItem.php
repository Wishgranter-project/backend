<?php

namespace WishgranterProject\Backend\Controller\Collection\Item;

use WishgranterProject\DescriptivePlaylist\PlaylistItem;

/**
 * Patches a playlist item.
 */
class PatchItem extends UpdateItem
{
    /**
     * Prepares an item to be updated.
     *
     * @param WishgranterProject\DescriptivePlaylist\PlaylistItem $item
     *   Playlist item.
     */
    protected function prepareItem($item): void
    {
        // do nothing.
    }
}
