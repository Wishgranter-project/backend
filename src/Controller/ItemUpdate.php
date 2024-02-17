<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\DescriptivePlaylist\PlaylistItem;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class ItemUpdate extends ControllerBase
{
    /**
     * @var AdinanCenci\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * @var AdinanCenci\Player\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param AdinanCenci\DescriptiveManager\PlaylistManager $playlistManager
     * @param AdinanCenci\Player\Service\Describer $describer
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
        if (!$item = $this->playlistManager->findItemByUuid($uuid, $playlistId)) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        $item->empty(['uuid', 'xxxOriginal']);

        $postData = $this->getPostData($request);
        foreach ($postData as $k => $v) {
            if ($item->isValidPropertyName($k)) {
                $item->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }

        $item->sanitize();
        $this->playlistManager->setItem($playlistId, $item);

        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->addSuccess(200, 'Item updates sucessfully')
            ->setData($data)
            ->renderResponse();
    }
}
