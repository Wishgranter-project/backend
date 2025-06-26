<?php

namespace WishgranterProject\Backend\Collection\Item\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Returns information about a playlist item.
 */
class ItemRead extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uuid = $request->getAttribute('itemUuid');
        $item = $this->playlistManager->getItemByUuid($uuid);

        if (!$item) {
            throw new NotFound('Item ' . $playlistId . ' does not exist.');
        }

        $data = $this->describer->describe($item);

        return $this->jsonResource($data)
            ->renderResponse();
    }
}
