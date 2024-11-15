<?php

namespace WishgranterProject\Backend\Collection\Playlist\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Retrieve items from a specific playlist.
 */
class PlaylistReadItems extends CollectionController
{
    use PaginationTrait;

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

        $results = $this->areFilterParametersPresent($request)
            ? $this->searchItems($request, $playlistId)
            : $this->simpleList($request, $playlistId);

        return JsonResource::fromSearchResults($results)
            ->renderResponse();
    }

    /**
     * Simply lists the items of the playlist.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param string $playlistId
     *   The id of the playlist.
     *
     * @return WishgranterProject\Backend\Helper\SearchResults
     *   Search results.
     */
    protected function simpleList(ServerRequestInterface $request, string $playlistId): SearchResults
    {
        list($page, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $playlist = $this->playlistManager->getPlaylist($playlistId);
        $total    = $playlist->lineCount - 1;
        $pages    = $this->numberPages($total, $itemsPerPage);
        $list     = $playlist->getItems(range($offset, $offset + $limit - 1));
        $count    = count($list);

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
     * Conduct a search in the playlist.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param string $playlistId
     *   The id of the playlist.
     *
     * @return WishgranterProject\Backend\Helper\SearchResults
     *   Search results.
     */
    protected function searchItems(ServerRequestInterface $request, $playlistId): SearchResults
    {
        list($page, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $playlist = $this->playlistManager->getPlaylist($playlistId);

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

        $results = $search->find();

        $total = count($results);
        $pages = $this->numberPages($total, $itemsPerPage);
        $list  = array_slice($results, $offset, $limit);
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
     * Checks if there are filter patameters in the request.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return bool
     *   If there are filter parameters.
     */
    protected function areFilterParametersPresent(ServerRequestInterface $request): bool
    {
        $params = $request->getQueryParams();

        return
            !empty($params['title']) ||
            !empty($params['genre']) ||
            !empty($params['artist']) ||
            !empty($params['soundtrack']);
    }
}
