<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Service\ServicesManager;

class PlaylistUpdate extends ControllerBase
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
