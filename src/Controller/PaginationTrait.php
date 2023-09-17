<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;

trait PaginationTrait 
{
    protected function getPage(ServerRequestInterface $request) : int
    {
        $page = (int) $request->get('page', 1);
        return $page > 0 
            ? $page 
            : 1;
    }

    protected function getItensPerPage(ServerRequestInterface $request) : int
    {
        $default = 20;

        $itensPerPage = (int) $request->get('itensPerPage', $default);
        $acceptable = [2, $default, 40, 100];

        return in_array($itensPerPage, $acceptable) 
            ? $itensPerPage
            : $default;
    }

    protected function getPaginationInfo(ServerRequestInterface $request) : array
    {
        $page          = $this->getPage($request);
        $itensPerPage  = $this->getItensPerPage($request);
        $offset        = ($page - 1) * $itensPerPage;
        $limit         = $itensPerPage;

        return [
            $page,
            $itensPerPage,
            $offset,
            $limit,
        ];
    }

    protected function numberPages(int $total, int $itensPerPage) : int
    {
        $pages = $total ? round($total / $itensPerPage) : 0;
        $pages += $total > $itensPerPage * $pages
            ? 1
            : 0;

        return $pages;
    }
}
