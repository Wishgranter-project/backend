<?php

namespace WishgranterProject\Backend\Collection\Item\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\Helper\JsonResource;

class ItemReadSearch extends CollectionController
{
    use PaginationTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $searchResults = $this->searchItems($request);

        return JsonResource::fromSearchResults($searchResults)
          ->renderResponse();
    }

    /**
     * Returns a page of search results.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\Backend\Helper\SearchResults
     *   The search results.
     */
    protected function searchItems(ServerRequestInterface $request): SearchResults
    {
        list($page, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $all   = $this->search($request);
        $total = count($all);
        $pages = $this->numberPages($total, $itemsPerPage);
        $list  = array_slice($all, $offset, $limit);
        $count = count($list);

        return new SearchResults(
            $list,
            $count,
            $page,
            $pages,
            $itemsPerPage,
            $total
        );
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
        $search = $this->playlistManager->search();

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

        $results = $search->find();

        $all = [];
        foreach ($results as $playlistId => $items) {
            $all = array_merge($all, $items);
        }

        return $all;
    }
}
