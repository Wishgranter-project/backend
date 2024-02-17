<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\DescriptivePlaylist\Playlist;

class PlaylistRead extends ControllerBase
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

        if (! $this->playlistManager->playlistExists($playlistId)) {
            throw new NotFound('Playlist ' . $playlistId . ' does not exist.');
        }

        $playlist = $this->playlistManager->getPlaylist($playlistId);

        return $request->get('download')
            ? $this->download($handler, $playlist)
            : $this->read($playlist);
    }

    protected function read(Playlist $playlist): ResponseInterface
    {
        $data = $this->describer->describe($playlist);

        $resource = new JsonResource();
        return $resource
            ->setData($data)
            ->renderResponse();
    }

    protected function download(RequestHandlerInterface $handler, Playlist $playlist): ResponseInterface
    {
        $file     = $playlist->fileName;

        $basename = basename($file);

        $response = $handler->responseFactory->ok(file_get_contents($file));
        $response = $response->withAddedHeader('content-type', 'application/jsonl');
        $response = $response->withAddedHeader('Content-Disposition', "attachment; filename=\"$basename\"");

        return $response;
    }
}
