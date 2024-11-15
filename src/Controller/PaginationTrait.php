<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;

trait PaginationTrait
{
    /**
     * Returns the page number requested for.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return int
     *   The page.
     */
    protected function getPage(ServerRequestInterface $request): int
    {
        $page = (int) $request->get('page', 1);
        return $page > 0
            ? $page
            : 1;
    }

    /**
     * Returns the maximum number of items each page should sport.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return int
     *   The number of items.
     */
    protected function getItemsPerPage(ServerRequestInterface $request): int
    {
        $default = $this->defaultItemsPerPage ?? 20;

        $itemsPerPage = (int) $request->get('itemsPerPage', $default);
        $acceptable = [2, 20, 40, 100];

        return in_array($itemsPerPage, $acceptable)
            ? $itemsPerPage
            : $default;
    }

    /**
     * Returns information about the pagination requested.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return array
     *   Pagination info.
     */
    protected function getPaginationInfo(ServerRequestInterface $request): array
    {
        $page          = $this->getPage($request);
        $itemsPerPage  = $this->getItemsPerPage($request);
        $offset        = ($page - 1) * $itemsPerPage;
        $limit         = $itemsPerPage;

        return [
            $page,
            $itemsPerPage,
            $offset,
            $limit,
        ];
    }

    /**
     * Given a number of results and items per page, how many pages there are ?
     *
     * @param int $totalNumberOfResults
     *   The number of results.
     * @param $itemsPerPage
     *   The maximum number of items each page should sport.
     *
     * @return int
     *   The number of pages.
     */
    protected function numberPages(int $totalNumberOfResults, int $itemsPerPage): int
    {
        $pages = $totalNumberOfResults ? round($totalNumberOfResults / $itemsPerPage) : 0;
        $pages += $totalNumberOfResults > $itemsPerPage * $pages
            ? 1
            : 0;

        return $pages;
    }
}
