<?php

namespace WishgranterProject\Backend\Controller\Collection\Item;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;
use WishgranterProject\DescriptiveManager\PlaylistManager;

/**
 * Creates a new playlist item.
 */
class ItemCreate extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);

        $post = $this->getPostData($request);

        $playlistId = $post['playlist'] ?? null;
        if (!$playlistId) {
            throw new \InvalidArgumentException('Inform a valid playlist id.');
        }
        unset($post['playlist']);

        if (!$collection->playlistExists($playlistId)) {
            throw new \InvalidArgumentException('Playlist ' . $playlistId . ' does not exist');
        }

        $uuid = $post['uuid'] ?? null;

        $item = $uuid
            ? $this->createItemByCopying($collection, $playlistId, $uuid)
            : $this->createItemFromScratch($collection, $playlistId, $post);

        $data = $this->describer->describe($item);

        return $this->jsonResource($data, 201)
            ->addSuccess(201, 'Item created')
            ->renderResponse();
    }

    /**
     * Creates a new playlist item by copying an existing one.
     *
     * Adds it to the end of the playlist.
     *
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $collection
     *   The user's collection.
     * @param string $playlistId
     *   The playlist id to add the new item to.
     * @param string $uuid
     *   The uuid of the item we are copying.
     *
     * @return WishgranterProject\DescriptivePlaylist\PlaylistItem
     *   The new playlist item.
     */
    protected function createItemByCopying(PlaylistManager $collection, string $playlistId, string $uuid): PlaylistItem
    {
        $original = $collection->getItemByUuid($uuid);

        if (!$original) {
            throw new \InvalidArgumentException('Item ' . $uuid . ' does not exist');
        }

        return $collection->addItem($playlistId, $original);
    }

    /**
     * Creates a new playlist item from scratch.
     *
     * Adds it to the end of the playlist.
     *
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $collection
     *   The user's collection.
     * @param string $playlistId
     *   The playlist id to add the new item to.
     * @param array $post
     *   Post data with the item properties.
     *
     * @return WishgranterProject\DescriptivePlaylist\PlaylistItem
     *   The new playlist item.
     */
    protected function createItemFromScratch(PlaylistManager $collection, string $playlistId, array $post): PlaylistItem
    {
        $item = new PlaylistItem();
        foreach ($post as $k => $v) {
            if ($item->isValidPropertyName($k)) {
                $item->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }
        $item->sanitize();

        return $collection->addItem($playlistId, $item);
    }
}
