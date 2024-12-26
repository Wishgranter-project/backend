<?php

namespace WishgranterProject\Backend\Collection\Playlist\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Lists the playlists.
 */
class PlaylistReadList extends CollectionController
{
    // I can't remember why I made this controller paginable.
    use PaginationTrait;

    /**
     * {@inheritdoc}
     */
    protected $defaultItemsPerPage = 100;

    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        list($page, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);

        $all   = $this->playlistManager->getAllPlaylists();
        $total = count($all);
        $pages = $this->numberPages($total, $itemsPerPage);
        $list  = array_slice($all, $offset, $limit);
        $count = count($list);

        if ($request->getHeaderLine('accept') == 'application/zip') {
            return $this->download($handler, $list);
        }

        $data  = [];
        usort($list, [$this, 'sortPlaylistByTitle']);
        foreach ($list as $playlistId => $playlist) {
            $data[] = $this->describer->describe($playlist);
        }

        $resource = new JsonResource();

        return $resource
            ->setMeta('total', $total)
            ->setMeta('itemsPerPage', $itemsPerPage)
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

    /**
     * Compacts the playlists into a zip file.
     *
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   Http request handler.
     * @param WishgranterProject\DescriptivePlaylist\Playlist[] $list
     *   Playlist objects.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    protected function download(RequestHandlerInterface $handler, array $list): ResponseInterface
    {
        $zipFile = tempnam(sys_get_temp_dir(), 'zip');
        register_shutdown_function('unlink', $zipFile);

        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::OVERWRITE);

        foreach ($list as $playlist) {
            $zip->addFile($playlist->fileName, basename($playlist->fileName));
        }

        $zip->close();

        $response = $handler->responseFactory->ok(file_get_contents($zipFile));
        $response = $response->withAddedHeader('content-type', 'application/zip');
        $response = $response->withAddedHeader('Content-Disposition', 'attachment; filename="your-collection.zip"');

        return $response;
    }
}
