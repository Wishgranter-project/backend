<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class ItemSearch extends ControllerBase 
{
    use PaginationTrait;

    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        list($page, $itensPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $all   = $this->search($request);
        $total = count($all);
        $pages = $this->numberPages($total, $itensPerPage);
        $list  = array_slice($all, $offset, $limit);
        $count = count($list);

        $data = [];
        foreach ($list as $item) {
            $data[] = $this->describer->describe($item);
        }

        $resource = new JsonResource();
        return $resource
            ->setMeta('total', $total)
            ->setMeta('itensPerPage', $itensPerPage)
            ->setMeta('pages', $pages)
            ->setMeta('page', $page)
            ->setMeta('count', $count)
            ->setData($data)
            ->renderResponse();
    }

    protected function search(ServerRequestInterface $request) : array
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
