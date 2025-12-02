<?php

namespace WishgranterProject\Backend\Controller\Collection\Item;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Deletes an item from the collection.
 */
class ItemDelete extends CollectionController
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

        $data = $this->describer->describe($item);

        return $this->jsonResource()
            ->addSuccess(200, 'Item deleted')
            ->renderResponse();
    }
}
