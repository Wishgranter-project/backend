<?php 
namespace AdinanCenci\Player\Discography;

use AdinanCenci\Player\Helper\SearchResults;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\LastFmApi;

class DiscographyLastFm implements DiscographyInterface 
{
    protected LastFmApi $api;

    public function __construct(LastFmApi $api) 
    {
        $this->api = $api;
    }

    public static function create() : DiscographyLastFm
    {
        return new self(ServicesManager::singleton()->get('lastFmApi'));
    }

    /**
     * @inheritDoc
     */
    public function searchForArtistByName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults
    {
        $info = $this->api->searchForArtist($artistName);

        $offset = $info->results->{'opensearch:startIndex'};
        $itensPerPage = $info->results->{'opensearch:itemsPerPage'};
        $total = $info->results->{'opensearch:totalResults'};

        $pages = round($total / $itensPerPage);
        $pages += $total > $itensPerPage * $pages ? 1 : 0;
        $page = $offset > 0 ? round($offset / $itensPerPage) : 1;

        $results = [];
        foreach ($info->results->artistmatches->artist as $r) {
            $results[] = Artist::createFromArray([
                'source' => 'lastfm',
                'id' => $r->mbid . '@lastfm',
                'name' => $r->name, 
                'thumbnail' => $r->image ? $r->image[0]->{'#text'} : ''
            ]);
        }

        return new SearchResults(
            $results,
            count($results),
            $page,
            $pages,
            $itensPerPage,
            $total
        );
    }

    /**
     * @inheritDoc
     */
    public function searchForReleasesByArtistName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults
    {
        $info = $this->api->getArtistAlbums($artistName, $page, $itensPerPage);

        $results = [];
        foreach ($info->topalbums->album as $album) {
            $results[] = Release::createFromArray([
                'source' => 'lastfm',
                'id' => ($album->mbid ? $album->mbid : $this->encodeBootlegId($album->artist->name, $album->name)) . '@lastfm',
                'title' => $album->name, 
                'thumbnail' => $album->image ? $album->image[2]->{'#text'} : null, 
                'year' => 0
            ]);
        }

        $attrs = $info->topalbums->{'@attr'};

        return new SearchResults(
            $results,
            count($results),
            $attrs->page,
            $attrs->totalPages,
            $attrs->perPage,
            $attrs->total
        );
    }

    /**
     * @inheritDoc
     */
    public function getReleaseById(string $releaseId) : Release
    {
        if ($this->isLegitId($releaseId)) {
            $data = $this->api->getRelease($releaseId);
        } else {
            list($artistName, $bootlegId) = $this->decodeBootlegId($releaseId);
            $data = $this->api->getReleaseByArtistAndTitle($artistName, $bootlegId);
        }

        $tracks = [];
        foreach ($data->album->tracks->track as $t) {
            $tracks[] = $t->name;
        }

        $release = Release::createFromArray([
            'source' => 'lastfm',
            'id' => $releaseId . '@lastfm',
            'title' => $data->album->name ?? '',
            'artist' => $data->album->artist,
            'thumbnail' => $data->album->image ? $data->album->image[2]->{'#text'} : null,
            'tracks' => $tracks
        ]);

        return $release;
    }

    protected function isLegitId(string $string) : bool
    {
        return substr_count($string, '-') >= 3;
    }

    protected function encodeBootlegId(string $artistName, string $albumTitle) : string
    {
        return base64_encode($artistName . '/' . $albumTitle);
    }

    protected function decodeBootlegId(string $bootlegId) : array
    {
        return explode('/', base64_decode($bootlegId));
    }

}
