<?php

namespace WishgranterProject\Backend\Controller\Collection\Item;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Helper\SearchResults;

/**
 * Searches for an item within the entire collection.
 */
class SearchItems extends CollectionController
{
    use PaginationTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        list($currentPage, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $items            = $this->search($request);
        $resultsCount     = count($items);
        $pagesCount       = $this->countPages($resultsCount, $itemsPerPage);
        $slice            = array_slice($items, $offset, $limit);
        $currentPageCount = count($slice);

        $data = array_map([$this, 'dataTransferItem'], $slice);

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
     * Searches for all matching items.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\DescriptivePlaylist\PlaylistItem[]
     *   Array of playlist items.
     */
    protected function search(ServerRequestInterface $request): array
    {
        $collection = $this->getCollection($request);

        $search = $collection->search();

        if ($request->get('playlist')) {
            $search->playlists($request->get('playlist'));
        }

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

        $results = $search->find();

        $all = array_values($results);

        return $all;
    }
}
