<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\DescriptivePlaylist\PlaylistItem;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Exception\NotFound;

class ItemCreate extends ControllerBase
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

        $postData = $this->getPostData($request);
        if (empty($postData['playlist']) || !is_string($postData['playlist'])) {
            throw new \InvalidArgumentException('Inform a playlist');
        }

        $playlistId = $postData['playlist'];
        unset($postData['playlist']);

        if (!$this->playlistManager->playlistExists($playlistId)) {
            throw new \InvalidArgumentException('Playlist ' . $playlistId . ' does not exist');
        }

        $uuid = isset($postData['uuid'])
            ? $postData['uuid']
            : null;

        $item = $uuid
            ? $this->createItemByCopying($playlistId, $uuid)
            : $this->createItemFromScratch($playlistId, $postData);

        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(201)
            ->addSuccess(201, 'Item created')
            ->setData($data)
            ->renderResponse();
    }

    protected function createItemByCopying(string $playlistId, string $uuid): PlaylistItem
    {
        $original = $this->playlistManager->getItemByUuid($uuid);

        if (!$original) {
            throw new \InvalidArgumentException('Item ' . $uuid . ' does not exist');
        }

        return $this->playlistManager->addItem($playlistId, $original);
    }

    protected function createItemFromScratch(string $playlistId, array $postData): PlaylistItem
    {
        $item = new PlaylistItem();
        foreach ($postData as $k => $v) {
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
