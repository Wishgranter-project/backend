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

/**
 * Service manager.
 *
 * Vary simple, very crude.
 */
class ServicesManager extends Singleton
{
    /**
     * The instances of different services.
     *
     * Instanciated as needed.
     *
     * @var array
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

    /**
     * Instantiates the playlist manager service.
     *
     * @return WishgranterProject\DescriptiveManager\PlaylistManager
     *   The service to manage playlists.
     */
    protected function isntantiatePlaylistManager()
    {
        $dir = defined('PLAYLISTS_DIR_TEST')
            ? PLAYLISTS_DIR_TEST
            : PLAYLISTS_DIR;

        return new PlaylistManager($dir);
    }

    /**
     * Instantiates the cache service.
     *
     * @return Psr\SimpleCache\CacheInterface
     *   Cache service.
     */
    protected function isntantiateCache()
    {
        $dir = defined('CACHE_DIR_TEST')
            ? CACHE_DIR_TEST
            : CACHE_DIR;

        return new Cache($dir);
    }

    /**
     * Instantiates the discography service.
     *
     * @return WishgranterProject\Backend\Service\Discography
     *   The discography service.
     */
    protected function isntantiateDiscography()
    {
        $cache          = $this->get('cache');
        $discogsApi     = new ApiDiscogs($this->get('config')->get('discogsToken', ''), [], $cache);
        $discogs        = new SourceDiscogs($discogsApi);

        $musicBrainzApi = new ApiMusicBrainz([], $cache);
        $musicBrainz    = new SourceMusicBrainz($musicBrainzApi);

        return new Discography([$discogs, $musicBrainz]);
    }

    /**
     * Instantiates the describer service.
     *
     * @return WishgranterProject\Backend\Service\Describer
     *   The describer service.
     */
    protected function isntantiateDescriber()
    {
        return Describer::create();
    }

    /**
     * Instantiates the service to find playable media.
     *
     * @return WishgranterProject\AetherMusic\Aether
     *   Aether service.
     */
    protected function isntantiateAether()
    {
        $youtubeApiKey = $this->get('config')->get('youtubeApiKey');

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

    /**
     * Alias for ::isntantiateConfiguration().
     *
     * @return WishgranterProject\Backend\Service\Configurations
     *   Configuration service.
     */
    protected function isntantiateConfig()
    {
        return $this->get('configuration');
    }

    /**
     * Instantiates the configuration service.
     *
     * @return WishgranterProject\Backend\Service\Configurations
     *   Configuration service.
     */
    protected function isntantiateConfiguration()
    {
        return Configurations::singleton();
    }
}
