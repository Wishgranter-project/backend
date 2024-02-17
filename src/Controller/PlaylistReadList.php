<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Service\ServicesManager;

class PlaylistReadList extends ControllerBase
{
    use PaginationTrait;

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
        list($page, $itensPerPage, $offset, $limit) = $this->getPaginationInfo($request);

        $all   = $this->playlistManager->getAllPlaylists();
        $total = count($all);
        $pages = $this->numberPages($total, $itensPerPage);
        $list  = array_slice($all, $offset, $limit);
        $count = count($list);

        return $request->get('download')
            ? $this->download($handler, $list)
            : $this->read($list, $total, $itensPerPage, $pages, $page, $count);
    }

    protected function download(RequestHandlerInterface $handler, array $playlists): ResponseInterface
    {
        $zipFile = tempnam(sys_get_temp_dir(), 'zip');
        register_shutdown_function('unlink', $zipFile);

        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::OVERWRITE);

        foreach ($playlists as $playlist) {
            $zip->addFile($playlist->fileName, basename($playlist->fileName));
        }

        $zip->close();

        $response = $handler->responseFactory->ok(file_get_contents($zipFile));
        $response = $response->withAddedHeader('content-type', 'application/zip');
        $response = $response->withAddedHeader('Content-Disposition', 'attachment; filename="your-collection.zip"');

        return $response;
    }

    protected function read($playlists, $total, $itensPerPage, $pages, $page, $count): ResponseInterface
    {
        $data  = [];
        usort($playlists, [$this, 'sortPlaylistByTitle']);
        foreach ($playlists as $playlistId => $playlist) {
            $data[] = $this->describer->describe($playlist);
        }

        $resource = new JsonResource();

        return $resource
            ->setMeta('total', $total)
            ->setMeta('itensPerPage', $itensPerPage)
            ->setMeta('pages', $pages)
            ->setMeta('page', $page)
            ->setMeta('count', $count)
            ->setData($data)
            ->renderResponse();
    }

    public function sortPlaylistByTitle($p1, $p2)
    {
        if (!$p1->title || !$p2->title) {
            return 0;
        }

        return strcasecmp($p1->title, $p2->title);
    }
}
