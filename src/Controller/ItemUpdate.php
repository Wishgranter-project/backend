<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\DescriptivePlaylist\PlaylistItem;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;

class ItemUpdate extends ControllerBase
{
    /**
     * @var WishgranterProject\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
     * @param WishgranterProject\Backend\Service\Describer $describer
     */
    public function __construct(PlaylistManager $playlistManager, Describer $describer)
    {
        $this->playlistManager = $playlistManager;
        $this->describer       = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new static(
            $servicesManager->get('playlistManager'),
            $servicesManager->get('describer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $uuid = $request->getAttribute('itemUuid');
        $currentPosition = null;
        if (!$item = $this->playlistManager->findItemByUuid($uuid, $playlistId, $currentPosition)) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        // Clears everything except for those two.
        $item->empty(['uuid', 'xxxOriginal']);

        $newPosition = null;
        $postData = $this->getPostData($request);
        foreach ($postData as $k => $v) {
            if ($k == 'position') {
                $newPosition = is_numeric($v) ? (int) $v : null;
            } elseif ($item->isValidPropertyName($k)) {
                $item->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }

        $item->sanitize();
        $this->playlistManager->setItem($playlistId, $item, $newPosition);

        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->addSuccess(200, 'Item updates sucessfully')
            ->setData($data)
            ->renderResponse();
    }
}
