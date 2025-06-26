<?php

namespace WishgranterProject\Backend\Collection\Item\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Updates a playlist item.
 */
class ItemUpdate extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uuid = $request->getAttribute('itemUuid');
        $currentPosition = null;
        $item = $this->playlistManager->findItemByUuid($uuid, $playlistId, $currentPosition);

        if (!$item) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        // Clears everything except for those two.
        $item->empty(['uuid', 'xxxOriginal']);

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

        $item->sanitize();
        $this->playlistManager->setItem($playlistId, $item, $newPosition);

        $data = $this->describer->describe($item);

        return $this->jsonResource($data)
            ->addSuccess(200, 'Item updated sucessfully.')
            ->renderResponse();
    }
}
