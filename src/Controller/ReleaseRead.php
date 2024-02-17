<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Service\ServicesManager;

class ReleaseRead extends ControllerBase
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
        $release = $this->getRelease($request);

        $data = $this->describer->describe($release);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($data)
            ->renderResponse();
    }

    protected function getRelease(ServerRequestInterface $request)
    {
        $releaseId = $request->getAttribute('releaseId');
        return $this->discographyDiscogs->getAlbumById($releaseId);
    }
}
