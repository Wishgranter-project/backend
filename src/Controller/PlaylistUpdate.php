<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Service\ServicesManager;

class PlaylistUpdate extends ControllerBase
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
        $playlistId = $request->getAttribute('playlist');

        $playlist = $this->playlistManager->getPlaylist($playlistId);
        if (! $playlist) {
            throw new NotFound('Playlist ' . $playlistName . ' not found.');
        }

        $header   = $playlist->getHeader();
        $header->empty();

        $postData = $this->getPostData($request);

        foreach ($postData as $k => $v) {
            if ($header->isValidPropertyName($k)) {
                $header->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }

        $playlist->setHeader($header);

        $resource = new JsonResource();
        $data = $this->describer->describe($playlist);

        return $resource
            ->setStatusCode(200)
            ->addSuccess(200, 'Changes saved')
            ->setData($data)
            ->renderResponse();
    }
}
