<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Exception\NotFound;

class ItemReadSearch extends ControllerBase
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
        $searchResults = $this->searchItems($request);

        return JsonResource::fromSearchResults($searchResults)
          ->renderResponse();
    }

    protected function searchItems(ServerRequestInterface $request): SearchResults
    {
        list($page, $itensPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $all   = $this->search($request);
        $total = count($all);
        $pages = $this->numberPages($total, $itensPerPage);
        $list  = array_slice($all, $offset, $limit);
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
