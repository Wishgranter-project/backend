<?php

namespace WishgranterProject\Backend\Controller\Collection;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;

/**
 * Lists all the artists within the collection.
 */
class ArtistsList extends ControllerBase
{
    /**
     * The service to manage playlists.
     *
     * @var WishgranterProject\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * Constructor.
     *
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
     *   The playlist manager service.
     */
    public function __construct(PlaylistManager $playlistManager)
    {
        $this->playlistManager = $playlistManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new self($servicesManager->get('playlistManager'));
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $artists  = $this->listArtists($request);

        return $this->jsonResource($artists)
            ->renderResponse();
    }

    /**
     * Lists all artist's names.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request.
     *
     * @return array
     *   An array of strings with all the artist's names.
     */
    protected function listArtists(ServerRequestInterface $request): array
    {
        $list = [];

        foreach ($this->playlistManager->getAllPlaylists() as $playlistId => $playlist) {
            foreach ($playlist->items as $key => $item) {
                if (! $item->artist) {
                    continue;
                }
                $this->countArtistsApperances((array) $item->artist, $list);
            }
        }

        krsort($list);
        arsort($list);

        return $list;
    }

    /**
     * Counts how many times the artist appears in the collection.
     *
     * @param array $itemArtists
     *   Artists names.
     * @param array
     *   Number of occurances, indexed by the artist's names.
     */
    protected function countArtistsApperances(array $itemArtists, &$list): void
    {
        foreach ($itemArtists as $a) {
            if (!isset($list[$a])) {
                $list[$a] = 1;
            } else {
                $list[$a]++;
            }
        }
    }
}
