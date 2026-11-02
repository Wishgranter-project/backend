<?php

namespace WishgranterProject\Backend\Controller\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\PaginationTrait;
use WishgranterProject\Backend\Helper\SearchResults;
use WishgranterProject\Backend\User\UserInterface;

class ListUsers extends UserController
{
    use PaginationTrait;

    /**
     * Invoke the controler.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     * @param Psr\Http\Server\RequestHandlerInterface $handler
     *   The request handler object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   The response object.
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        list($currentPage, $itemsPerPage, $offset, $limit) = $this->getPaginationInfo($request);
        $items            = $this->search($request);
        $resultsCount     = count($items);
        $pagesCount       = $this->countPages($resultsCount, $itemsPerPage);
        $slice            = array_slice($items, $offset, $limit);
        $currentPageCount = count($slice);

        $data = array_map([$this, 'dataTransferUser'], $slice);

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
     * Searches for all matching users.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\DescriptivePlaylist\PlaylistItem[]
     *   Array of users.
     */
    protected function search(ServerRequestInterface $request)
    {
        $users = [];
        $username = strtolower($request->get('username'));

        foreach ($this->userManager->getAllUsers() as $user) {
            if ($username && !substr_count(strtolower($user->getUsername()), $username)) {
                continue;
            }

            $users[] = $user;
        }

        return $users;
    }

    /**
     * Generates a data transfer object out of a given user object.
     *
     * @param WishgranterProject\Backend\User\UserInterface $user
     *   A playlist object.
     *
     * @return array
     *   Data for transfer.
     */
    protected function dataTransferUser(UserInterface $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ];
    }
}
