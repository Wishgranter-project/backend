<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Lists the playlists.
 */
class ListPlaylists extends CollectionController
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
        if (is_null($collection)) {
            throw new NotFound();
        }

        list($currentPage, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);

        $playlists        = $collection->getAllPlaylists();
        $resultsCount     = count($playlists);
        $pagesCount       = $this->countPages($resultsCount, $itemsPerPage);
        $slice            = array_slice($playlists, $offset, $limit);
        $currentPageCount = count($slice);

        usort($slice, [$this, 'sortPlaylistByTitle']);
        $data = array_map([$this, 'dataTransferPlaylist'], $slice);

        $searchResults = new SearchResults(
            $data,
            $currentPageCount,
            $currentPage,
            $pagesCount,
            $itemsPerPage,
            $resultsCount,
        );

        return $searchResults->renderResponse();
    }

    /**
     * Sort playlist objects by their titles.
     *
     * @param WishgranterProject\DescriptivePlaylist\Playlist $p1
     *   Playlist.
     * @param WishgranterProject\DescriptivePlaylist\Playlist $p2
     *   Playlist.
     *
     * @return int
     *   To sort.
     */
    public function sortPlaylistByTitle(Playlist $p1, Playlist $p2): int
    {
        if (!$p1->title || !$p2->title) {
            return 0;
        }

        return strcasecmp($p1->title, $p2->title);
    }
}
