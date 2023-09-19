<?php
namespace AdinanCenci\Player\Source;

use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\YoutubeApi;

class SourceYoutube extends SourceAbstract implements SourceInterface 
{
    protected YoutubeApi $api;

    public function __construct(YoutubeApi $api) 
    {
        $this->api = $api;
    }

    public static function create() : SourceYoutube
    {
        return new self(ServicesManager::singleton()->get('youtubeApi'));
    }

    public function search(array $parameters) : array
    {
        $query = $this->buildQuery($parameters);
        $data  = $this->api->searchVideos($query);

        $resources = [];
        foreach ($data->items as $item) {
            if ($item->id->kind != 'youtube#video') {
                continue;
            }

            $resources[] = new Resource(
                'youtube',
                $item->id->videoId . '@youtube',
                htmlspecialchars_decode($item->snippet->title),
                htmlspecialchars_decode($item->snippet->description),
                $item->snippet->thumbnails->default->url
            );
        }

        return $resources;
    }
}
