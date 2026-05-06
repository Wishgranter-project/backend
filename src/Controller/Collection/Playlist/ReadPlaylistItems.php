<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\DescriptiveManager\PlaylistManager;

/**
 * Retrieve items from a specific playlist.
 */
class ReadPlaylistItems extends CollectionController
{
    use PaginationTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $collection = $this->getCollection($request);

        $playlistId = $request->getAttribute('playlist');
        if (! $collection->playlistExists($playlistId)) {
            throw new NotFound('Playlist ' . $playlistId . ' does not exist.');
        }

        $results = $this->areFilterParametersPresent($request)
            ? $this->searchItems($request, $collection, $playlistId)
            : $this->simpleList($request, $collection, $playlistId);

        return $results->renderResponse();
    }

    /**
     * Simply lists the items of the playlist.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $collection
     *   The user's collection.
     * @param string $playlistId
     *   The id of the playlist.
     *
     * @return WishgranterProject\Backend\Helper\SearchResults
     *   Search results.
     */
    protected function simpleList(
        ServerRequestInterface $request,
        PlaylistManager $collection,
        string $playlistId
    ): SearchResults {

        list($currentPage, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $playlist         = $collection->getPlaylist($playlistId);
        $resultsCount     = $playlist->lineCount - 1;
        $pagesCount       = $this->countPages($resultsCount, $itemsPerPage);
        $slice            = $playlist->getItems(range($offset, $offset + $limit - 1));
        $currentPageCount = count($slice);

        $data = array_map([$this, 'dataTransferItem'], $slice);

        return new SearchResults(
            $data,
            $currentPageCount,
            $currentPage,
            $pagesCount,
            $itemsPerPage,
            $resultsCount,
        );
    }

    /**
     * Conduct a search in the playlist.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $collection
     *   The user's collection.
     * @param string $playlistId
     *   The id of the playlist.
     *
     * @return WishgranterProject\Backend\Helper\SearchResults
     *   Search results.
     */
    protected function searchItems(
        ServerRequestInterface $request,
        PlaylistManager $collection,
        $playlistId
    ): SearchResults {
        list($currentPage, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $playlist = $collection->getPlaylist($playlistId);

        $search = $playlist->search();

        if ($request->get('title')) {
            $search->condition('title', $request->get('title'), 'LIKE');
        }

        if ($request->get('genre')) {
            $search->condition('genre', $request->get('genre'), 'LIKE');
        }

        if ($request->get('artist')) {
            $search->condition('artist', $request->get('artist'), 'LIKE');
        }

        if ($request->get('soundtrack')) {
            $search->condition('soundtrack', $request->get('soundtrack'), 'LIKE');
        }

        if ($request->get('orderBy')) {
            $search->orderBy($request->get('orderBy'), $request->get('order', 'ASC'));
        }

        $results          = $search->find();
        $resultsCount     = count($results);
        $pagesCount       = $this->countPages($resultsCount, $itemsPerPage);
        $slice            = array_slice($results, $offset, $limit);
        $currentPageCount = count($slice);

        $data = array_map([$this, 'dataTransferItem'], $slice);

        return new SearchResults(
            $data,
            $currentPageCount,
            $currentPage,
            $pagesCount,
            $itemsPerPage,
            $resultsCount,
        );
    }

    /**
     * Checks if there are filter patameters in the request.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return bool
     *   True if there are filter parameters.
     */
    protected function areFilterParametersPresent(ServerRequestInterface $request): bool
    {
        $params = $request->getQueryParams();

        return
            !empty($params['title']) ||
            !empty($params['genre']) ||
            !empty($params['artist']) ||
            !empty($params['soundtrack']) ||
            !empty($params['orderBy']);
    }
}
