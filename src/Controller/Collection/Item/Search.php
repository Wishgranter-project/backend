<?php 
namespace AdinanCenci\Player\Controller\Collection\Item;

use AdinanCenci\DescriptivePlaylist\Playlist;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Helper\JsonResource;

class Search extends ControllerBase 
{
    public function formResponse($request, $handler) 
    {
        $object = $this->search($request, $cacheHit);

        $resource = new JsonResource();
        return $resource
            ->merge($object)
            ->cacheHit($cacheHit)
            ->getResponse();
    }

    protected function search($request, &$cacheHit = false) 
    {
        $cacheHit     = false;
        $page         = (int) $request->get('page', 0);
        $page         = $page < 0 ? 0 : $page;
        $itensPerPage = 32;
        $offset       = $page * $itensPerPage;
        $limit        = $offset + $itensPerPage;

        $cacheKeys = ['search', 'collection', md5($request->getUri()->getQuery())];

        if ($this->cacheManager->has($cacheKeys)) {
            $cacheHit = true;
            return $this->cacheManager->get($cacheKeys);
        }

        $object   = [
            'page'  => $page,
            'items' => []
        ];

        foreach ($this->playlistManager->getPlaylists() as $playlistId => $playlist) {
            $search   = $playlist->search();

            if ($title = $request->get('title')) {
                $search->condition('title', $title, 'LIKE');
            }

            if ($artist = $request->get('artist')) {
                $search->condition('artist', $artist, 'LIKE');
            }

            if ($soundtrack = $request->get('soundtrack')) {
                $search->condition('soundtrack', $soundtrack, 'LIKE');
            }

            if ($genre = $request->get('genre')) {
                $search->condition('genre', $genre, 'LIKE');
            }

            $results = array_values($search->find());

            foreach ($results as $key => $item) {
                if ($key >= $limit) {
                    break 2;
                }
                if ($key < $offset) {
                    continue;
                }
                $data = $item->getData();
                $data->xxxPlaylist = $playlistId;
                $object['items'][] = $data;
            }
        }

        $this->cacheManager->set($cacheKeys, $object);
        return $object;
    }
}
