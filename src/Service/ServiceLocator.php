<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Backend\Authentication\AuthenticationManager;
use WishgranterProject\Backend\Helper\Singleton;
use WishgranterProject\Backend\Session\SessionManager;
use WishgranterProject\Backend\Session\SessionGarbageCollector;
use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\DiscographyDiscogs\ApiDiscogs;
use WishgranterProject\DiscographyDiscogs\SourceDiscogs;
use WishgranterProject\DiscographyMusicBrainz\ApiMusicBrainz;
use WishgranterProject\DiscographyMusicBrainz\SourceMusicBrainz;
use WishgranterProject\YouTubeProbe\YouTubeApi;
use WishgranterProject\YouTubeProbe\YouTubeProbe;
use WishgranterProject\LocalFilesProbe\LocalFilesProbe;
use WishgranterProject\MusicRadar\Radar;
use AdinanCenci\FileCache\Cache;

/**
 * Service locator.
 *
 * Very simple, very crude.
 */
class ServiceLocator extends Singleton
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
            $this->services[$serviceId] = $this->instantiate($serviceId);
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
    protected function instantiate(string $serviceId)
    {
        $method = 'instantiate' . ucfirst($serviceId);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    /**
     * Instantiates the collection manager service.
     *
     * @return WishgranterProject\Backend\Service\CollectionManager
     *   The service to manage playlists.
     */
    protected function instantiateCollectionManager()
    {
        return new CollectionManager(DIR_COLLECTIONS);
    }

    /**
     * Instantiates the cache service.
     *
     * @return Psr\SimpleCache\CacheInterface
     *   Cache service.
     */
    protected function instantiateCache()
    {
        return new Cache(DIR_CACHE);
    }

    /**
     * Instantiates the discography service.
     *
     * @return WishgranterProject\Backend\Service\Discography
     *   The discography service.
     */
    protected function instantiateDiscography()
    {
        $cache          = $this->get('cache');
        $discogsApi     = new ApiDiscogs($this->get('config')->get('discogsToken', ''), [], $cache);
        $discogs        = new SourceDiscogs($discogsApi);

        $musicBrainzApi = new ApiMusicBrainz([], $cache);
        $musicBrainz    = new SourceMusicBrainz($musicBrainzApi);

        return new Discography([$discogs, $musicBrainz]);
    }

    /**
     * Instantiates the service to find playable media.
     *
     * @return WishgranterProject\MusicRadar\Radar
     *   Music radar service.
     */
    protected function instantiateRadar()
    {
        $youtubeApiKey = $this->get('config')->get('youtubeApiKey');

        $radar = new Radar();

        $apiYouTube  = new YouTubeApi($youtubeApiKey, [], $this->get('cache'));
        $youTube     = new YouTubeProbe($apiYouTube);

        if (file_exists(DIR_LOCAL_MEDIA)) {
            $localFiles = new LocalFilesProbe(DIR_LOCAL_MEDIA, 'https://wishgranter-backend.ddev.site/' . basename(DIR_LOCAL_MEDIA) . '/');
            $radar->addProbe($localFiles, 20);
        }

        $radar->addProbe($youTube, 1);
        return $radar;
    }

    /**
     * Alias for ::instantiateConfiguration().
     *
     * @return WishgranterProject\Backend\Service\Configurations
     *   Configuration service.
     */
    protected function instantiateConfig()
    {
        return $this->get('configuration');
    }

    /**
     * Instantiates the configuration service.
     *
     * @return WishgranterProject\Backend\Service\Configurations
     *   Configuration service.
     */
    protected function instantiateConfiguration()
    {
        return Configurations::singleton();
    }

    /**
     * Instantiates the user manager service.
     *
     * @return WishgranterProject\Backend\User\UserManager
     *   User manager service.
     */
    protected function instantiateUserManager()
    {
        return new UserManager(\DIR_USERS);
    }

    /**
     * Instantiates the authentication service.
     *
     * @return WishgranterProject\Backend\Authentication\AuthenticationManager
     *   Authentication service.
     */
    protected function instantiateAuthentication()
    {
        return new AuthenticationManager($this);
    }

    /**
     * Instantiates the session manager service.
     *
     * @return WishgranterProject\Backend\Session\SessionManager
     *   Session manager service.
     */
    protected function instantiateSessionManager()
    {
        return new SessionManager(\DIR_SESSIONS, $this->get('userManager'));
    }

    /**
     * Instantiates the session garbage collector service.
     *
     * @return WishgranterProject\Backend\Session\SessionGarbageCollector
     *   Session garbage collector.
     */
    protected function instantiateSessionGarbageCollector()
    {
        return new SessionGarbageCollector($this->get('sessionManager'));
    }

    /**
     * Instantiates the settings service.
     *
     * @return WishgranterProject\Backend\Service\Settings
     *   Settings service
     */
    protected function instantiateSettings()
    {
        return new Settings();
    }
}
