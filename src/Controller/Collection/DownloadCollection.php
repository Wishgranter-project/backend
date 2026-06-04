<?php

namespace WishgranterProject\Backend\Controller\Collection;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Download the entire collection.
 */
class DownloadCollection extends CollectionController
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
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);
        $playlists  = $collection->getAllPlaylists();
        $zipFile    = tempnam(sys_get_temp_dir(), 'zip');
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
}
