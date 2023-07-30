<?php 
namespace AdinanCenci\Player\Controller\Collection\Playlist\Item;

use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\DescriptivePlaylist\PlaylistItem;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class Sources extends ControllerBase 
{
    public function formResponse($request, $handler) 
    {
        $object = $this->find($request, $cacheHit);

        $resource = new JsonResource();
        return $resource
            ->merge($object)
            ->cacheHit($cacheHit)
            ->getResponse();

        return $response;
    }

    protected function find($request, &$cacheHit = false) 
    {
        $cacheHit  = false;
        $itemId    = $request->getAttribute('item');
        $cacheKeys = [$itemId, 'sources'];

        if ($this->cacheManager->has($cacheKeys)) {
            $cacheHit = true;
            return $this->cacheManager->get($cacheKeys);
        }

        $item    = $this->getItem($request);
        $query   = $this->getQuery($item);
        $ids     = $this->youtubeSearcher->search($query);
        $objects = [
            'sources' => []
        ];

        foreach ($ids as $id) {
            $objects['sources'][] = [
                'service' => 'youtube',
                'id' => $id
            ];
        }

        $this->cacheManager->set($cacheKeys, $objects);
        return $objects;
    }

    protected function getItem($request) 
    {
        $playlistTitle = $request->getAttribute('playlist');
        $file          = $this->playlistManager->file($playlistTitle);
        $itemId        = $request->getAttribute('item');

        if (! file_exists($file)) {
            throw new NotFound('Playlist ' . $playlistTitle . ' not found.');
        }

        $playlist      = new Playlist($file);
        $item          = $playlist->getItemByUuid($itemId);

        if (! $item) {
            throw new NotFound('Item ' . $itemId . ' not found.');
        }

        return $item;
    }

    protected function getQuery(PlaylistItem $item) : string
    {
        $query = ($item->title ?? $item->album);

        if ($item->soundtrack) {
            $query .= ' ' . implode(' ', (array) $item->soundtrack);
        } else if ($item->artist) {
            $query .= ' ' . implode(' ', (array) $item->artist);
        }

        return $query;
    }
}
