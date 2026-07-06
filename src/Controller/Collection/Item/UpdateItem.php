<?php

namespace WishgranterProject\Backend\Controller\Collection\Item;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;

/**
 * Updates a playlist item.
 */
class UpdateItem extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);

        $uuid = $request->getAttribute('itemUuid');
        $currentPosition = null;
        $item = $collection->findItemByUuid($uuid, $playlistId, $currentPosition);

        if (!$item) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        $this->prepareItem($item);

        $newPosition = null;
        $post = $this->getPostData($request);
        foreach ($post as $key => $v) {
            if ($key == 'position') {
                $newPosition = is_numeric($v)
                    ? (int) $v
                    : null;
            } elseif ($item->isValidPropertyName($key)) {
                $item->{$key} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $key);
            }
        }

        while (!$collection->lock()) {
            // loop until the lock is acquired.
        }

        $item->sanitize();
        $collection->setItem($playlistId, $item, $newPosition);

        $data = $this->dataTransferItem($item);

        $collection->unlock();

        return $this->jsonResource($data)
            ->addSuccess(200, 'Item updated sucessfully.')
            ->renderResponse();
    }

    /**
     * Prepares an item to be updated.
     *
     * @param WishgranterProject\DescriptivePlaylist\PlaylistItem $item
     *   Playlist item.
     */
    protected function prepareItem($item): void
    {
        // Clears everything except for those two.
        $item->empty(['uuid', 'xxxOriginal']);
    }
}
