<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Backend\Helper\Singleton;
use WishgranterProject\Discography\Discogs\ApiDiscogs;
use WishgranterProject\Discography\Discogs\Source\SourceDiscogs;
use WishgranterProject\Discography\MusicBrainz\ApiMusicBrainz;
use WishgranterProject\Discography\MusicBrainz\Source\SourceMusicBrainz;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\AetherMusic\YouTube\YouTubeApi;
use WishgranterProject\AetherMusic\YouTube\Source\SourceYouTube;
use WishgranterProject\AetherMusic\YouTube\Source\SourceYouTubeLax;
use WishgranterProject\AetherMusic\LocalFiles\Source\SourceLocalFiles;
use WishgranterProject\AetherMusic\Aether;
use AdinanCenci\FileCache\Cache;

class ServicesManager extends Singleton
{
    /**
     * @var array
     *   The instances of different services.
     *   Instanciated as needed.
     */
    protected array $services = [];

    /**
     * Retrieves a service object.
     *
     * Attempts to instantiate if necessary.
     *
     * @param string $serviceId
     *   String identifying the service.
     *
     * @throws \InvalidArgumentException
     */
    public function get(string $serviceId)
    {
        if (! isset($this->services[$serviceId])) {
            $this->services[$serviceId] = $this->isntantiate($serviceId);
        }

        if (is_null($this->services[$serviceId])) {
            throw new \InvalidArgumentException('Service ' . $serviceId . ' not found.');
        }

        return $this->services[$serviceId];
    }

    /**
     * Attempts to instantiate a service.
     *
     * @param string $serviceId
     *   String identifying the service.
     *
     * @return mixed|null
     *   Returns null if it cannot instantiate the service.
     */
    protected function isntantiate(string $serviceId)
    {
        $method = 'isntantiate' . ucfirst($serviceId);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    protected function isntantiatePlaylistManager()
    {
        $dir = defined('PLAYLISTS_DIR_TEST')
            ? PLAYLISTS_DIR_TEST
            : PLAYLISTS_DIR;

        return new PlaylistManager($dir);
    }

    protected function isntantiateCache()
    {
        $dir = defined('CACHE_DIR_TEST')
            ? CACHE_DIR_TEST
            : CACHE_DIR;

        return new Cache($dir);
    }

    protected function isntantiateResourceFinder()
    {
        return ResourceFinder::create();
    }

    protected function isntantiateDiscography()
    {
        $cache          = $this->get('cache');
        $discogsApi     = new ApiDiscogs($this->get('config')->get('discogsToken', ''), [], $cache);
        $discogs        = new SourceDiscogs($discogsApi);

        $musicBrainzApi = new ApiMusicBrainz([], $cache);
        $musicBrainz    = new SourceMusicBrainz($musicBrainzApi);

        return new Discography([$discogs, $musicBrainz]);
    }

    protected function isntantiateDescriber()
    {
        return Describer::create();
    }

    protected function isntantiateAether()
    {
        $config = $this->get('config');
        $youtubeApiKey = $config->get('youtubeApiKey');

        $aether = new Aether();

        $apiYouTube  = new YouTubeApi($youtubeApiKey, [], $this->get('cache'));
        $youTube     = new SourceYouTube($apiYouTube);
        $youTubeLax  = new SourceYouTubeLax($apiYouTube);

        if (file_exists(LOCAL_FILES_DIR)) {
            $localFiles = new SourceLocalFiles(LOCAL_FILES_DIR, 'http://player-backend.lndo.site:8000/');
            $aether->addSource($localFiles, 20);
        }

        $aether->addSource($youTube, 1);
        $aether->addSource($youTubeLax, 2);
        return $aether;
    }

    protected function isntantiateConfig()
    {
        return $this->get('configuration');
    }

    protected function isntantiateConfiguration()
    {
        return Configurations::singleton();
    }
}
