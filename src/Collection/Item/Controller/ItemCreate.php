<?php

namespace WishgranterProject\Backend\Collection\Item\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;

/**
 * Creates a new playlist item.
 */
class ItemCreate extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $post = $this->getPostData($request);

        $playlistId = $post['playlist'] ?? null;
        if (!$playlistId) {
            throw new \InvalidArgumentException('Inform a valid playlist id.');
        }
        unset($post['playlist']);

        if (!$this->playlistManager->playlistExists($playlistId)) {
            throw new \InvalidArgumentException('Playlist ' . $playlistId . ' does not exist');
        }

        $uuid = $post['uuid'] ?? null;

        $item = $uuid
            ? $this->createItemByCopying($playlistId, $uuid)
            : $this->createItemFromScratch($playlistId, $post);

        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(201)
            ->addSuccess(201, 'Item created')
            ->setData($data)
            ->renderResponse();
    }

    /**
     * Creates a new playlist item by copying an existing one.
     *
     * Adds it to the end of the playlist.
     *
     * @param string $playlistId
     *   The playlist id to add the new item to.
     * @param string $uuid
     *   The uuid of the item we are copying.
     *
     * @return WishgranterProject\DescriptivePlaylist\PlaylistItem
     *   The new playlist item.
     */
    protected function createItemByCopying(string $playlistId, string $uuid): PlaylistItem
    {
        $original = $this->playlistManager->getItemByUuid($uuid);

        if (!$original) {
            throw new \InvalidArgumentException('Item ' . $uuid . ' does not exist');
        }

        return $this->playlistManager->addItem($playlistId, $original);
    }

    /**
     * Creates a new playlist item from scratch.
     *
     * Adds it to the end of the playlist.
     *
     * @param string $playlistId
     *   The playlist id to add the new item to.
     * @param array $post
     *   Post data with the item properties.
     *
     * @return WishgranterProject\DescriptivePlaylist\PlaylistItem
     *   The new playlist item.
     */
    protected function createItemFromScratch(string $playlistId, array $post): PlaylistItem
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

        return $this->playlistManager->addItem($playlistId, $item);
    }
}
