<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\Discography\Source\SearchResults;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Exception\NotFound;

class ItemReadSearch extends ControllerBase
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
