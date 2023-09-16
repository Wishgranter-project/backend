<?php 
namespace AdinanCenci\Player\Discography;

use AdinanCenci\Player\Helper\SearchResults;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\DiscogsApi;

class DiscographyDiscogs implements DiscographyInterface 
{
    protected DiscogsApi $api;

    public function __construct(DiscogsApi $api) 
    {
        $this->api = $api;
    }

    public static function create() : DiscographyDiscogs
    {
        return new self(ServicesManager::singleton()->get('discogsApi'));
    }

    /**
     * @inheritDoc
     */
    public function searchForArtistByName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults
    {
        $info = $this->api->searchForArtist($artistName);

        $results = [];
        foreach ($info->results as $r) {
            $results[] = Artist::createFromArray([
                'source' => 'discogs',
                'id' => $r->id . '@discogs',
                'name' => $r->title, 
                'thumbnail' => $r->thumb ?? ''
            ]);
        }

        return new SearchResults(
            $results,
            count($results),
            $info->pagination->page,
            $info->pagination->pages,
            $info->pagination->per_page,
            $info->pagination->items
        );
    }

    /**
     * @inheritDoc
     */
    public function searchForReleasesByArtistName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults
    {
        $info = $this->api->getArtistAlbums($artistName);

        $results = [];
        foreach ($info->results as $r) {
            $results[] = Release::createFromArray([
                'source' => 'discogs',
                'id' => $r->master_id . '@discogs',
                'title' => $r->title, 
                'thumbnail' => $r->thumb ?? null, 
                'year' => ((int) $r->year ?? 0)
            ]);
        }

        return new SearchResults(
            $results,
            count($results),
            $info->pagination->page,
            $info->pagination->pages,
            $info->pagination->per_page,
            $info->pagination->items
        );
    }

    /**
     * @inheritDoc
     */
    public function getReleaseById(string $releaseId) : Release
    {
        $data = $this->api->getRelease($releaseId);

        $tracks = [];
        foreach ($data->tracklist as $t) {
            $tracks[] = $t->title;
        }

        $release = Release::createFromArray([
            'source' => 'discogs',
            'id' => $releaseId,
            'title' => $data->title ?? '',
            'artist' => $data->artists[0]->name,
            'thumbnail' => isset($data->images[0]) ? $data->images[0]->uri : null,
            'tracks' => $tracks
        ]);

        return $release;
    }
}
