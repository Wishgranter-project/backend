<?php

namespace WishgranterProject\Backend\Controller\Collection\Item;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;

/**
 * Deletes an item from the collection.
 */
class DeleteItem extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);

        $uuid = $request->getAttribute('itemUuid');
        $item = $collection->findItemByUuid($uuid, $playlistId);

        if (!$item) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        $playlist = $collection->getPlaylist($playlistId);
        $playlist->deleteItem($item);

        $data = $this->dataTransferItem($item);

        return $this->jsonResource()
            ->addSuccess(200, 'Item deleted')
            ->renderResponse();
    }
}
