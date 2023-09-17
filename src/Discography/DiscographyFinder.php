<?php 
namespace AdinanCenci\Player\Discography;

use AdinanCenci\Player\Helper\SearchResults;

use AdinanCenci\Player\Service\ServicesManager;

class DiscographyFinder implements DiscographyInterface
{
    protected array $sources;

    public function __construct(array $sources) 
    {
        $this->sources = $sources;
    }

    public static function create() : DiscographyFinder 
    {
        $serviceManager = ServicesManager::singleton();

        return new self(
            [
                'lastfm' => $serviceManager->get('discographyLastFm'),
                'discogs' => $serviceManager->get('discographyDiscogs'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function searchForArtistByName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults
    {
        foreach ($this->sources as $source) {
            $results = $source->searchForArtistByName($artistName, $page, $itensPerPage);
            if ($results->total) {
                return $results;
            }
        }

        return SearchResults::empty();
    }

    /**
     * @inheritDoc
     */
    public function searchForReleasesByArtistName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults
    {
        foreach ($this->sources as $source) {
            $results = $source->searchForReleasesByArtistName($artistName, $page, $itensPerPage);
            if ($results->total) {
                return $results;
            }
        }

        return SearchResults::empty();
    }

    /**
     * @inheritDoc
     */
    public function getReleaseById(string $releaseId) : Release
    {
        if (!substr_count($releaseId, '@')) {
            throw new \InvalidArgumentException('Provide a valid release id, you cunt');
        }

        list($id, $source) = explode('@', $releaseId);

        if (empty($id)) {
            throw new \InvalidArgumentException('Provide a valid release id, you cunt');
        }

        return $this->sources[$source]->getReleaseById($id);
    }
}
