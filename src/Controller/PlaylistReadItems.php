<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\DescriptivePlaylist\Playlist;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Exception\NotFound;

class PlaylistReadItems extends ControllerBase
{
    use PaginationTrait;

    /**
     * @var WishgranterProject\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
     * @param WishgranterProject\Backend\Service\Describer $describer
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

        $results = $this->filterParametersPresent($request)
            ? $this->searchItems($request, $playlistId)
            : $this->simpleList($request, $playlistId);

        return JsonResource::fromSearchResults($results)
            ->renderResponse();
    }

    protected function simpleList(ServerRequestInterface $request, $playlistId): SearchResults
    {
        list($page, $itensPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $playlist = $this->playlistManager->getPlaylist($playlistId);
        $total    = $playlist->lineCount - 1;
        $pages    = $this->numberPages($total, $itensPerPage);
        $list     = $playlist->getItems(range($offset, $offset + $limit - 1));
        $count    = count($list);

        return new SearchResults(
            $list,
            $count,
            $page,
            $pages,
            $itensPerPage,
            $total
        );
    }

    protected function searchItems(ServerRequestInterface $request, $playlistId): SearchResults
    {
        list($page, $itensPerPage, $offset, $limit) = $this->getPaginationInfo($request);
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
        $pages = $this->numberPages($total, $itensPerPage);
        $list  = array_slice($results, $offset, $limit);
        $count = count($list);

        return new SearchResults(
            $list,
            $count,
            $page,
            $pages,
            $itensPerPage,
            $total
        );
    }

    protected function filterParametersPresent(ServerRequestInterface $request): bool
    {
        $params = $request->getQueryParams();

        return
            !empty($params['title']) ||
            !empty($params['genre']) ||
            !empty($params['artist']) ||
            !empty($params['soundtrack']);
    }
}
