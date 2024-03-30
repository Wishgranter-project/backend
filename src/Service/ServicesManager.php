<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Discography\Discogs\ApiDiscogs;
use WishgranterProject\Discography\Discogs\SourceDiscogs;
use WishgranterProject\Discography\MusicBrainz\ApiMusicBrainz;
use WishgranterProject\Discography\MusicBrainz\SourceMusicBrainz;
use WishgranterProject\DescriptiveManager\PlaylistManager;
use WishgranterProject\AetherMusic\Api\ApiYouTube;
use WishgranterProject\AetherMusic\Api\ApiSliderKz;
use WishgranterProject\AetherMusic\Source\SourceYouTube;
use WishgranterProject\AetherMusic\Source\SourceSliderKz;
use WishgranterProject\AetherMusic\Source\SourceLocalFiles;
use WishgranterProject\AetherMusic\Aether;
use AdinanCenci\FileCache\Cache;

class ServicesManager extends Singleton
{
    protected $services = [];

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

    protected function isntantiate(string $serviceId)
    {
        switch ($serviceId) {
            case 'playlistManager':
                $dir = defined('PLAYLISTS_DIR_TEST')
                    ? PLAYLISTS_DIR_TEST
                    : PLAYLISTS_DIR;

                return new PlaylistManager($dir);
                break;
            case 'cache':
                $dir = defined('CACHE_DIR_TEST')
                    ? CACHE_DIR_TEST
                    : CACHE_DIR;

                return new Cache($dir);
                break;
            case 'resourceFinder':
                return ResourceFinder::create();
                break;
            case 'discography':
                $cache          = $this->get('cache');
                $discogsApi     = new ApiDiscogs($this->get('config')->get('discogsToken', ''), [], $cache);
                $discogs        = new SourceDiscogs($discogsApi);
                $musicBrainzApi = new ApiMusicBrainz([], $cache);
                $musicBrainz    = new SourceMusicBrainz($musicBrainzApi);

                return new Discography([$discogs, $musicBrainz]);
                break;
            case 'describer':
                return Describer::create();
                break;
            case 'aether':
                $config = $this->get('config');
                $youtubeApiKey = $config->get('youtubeApiKey');

                $aether = new Aether();

                $apiYouTube  = new ApiYouTube($youtubeApiKey, [], $this->get('cache'));
                $youTube     = new SourceYouTube($apiYouTube);

                //$apiSliderKz = new ApiSliderKz([], $this->get('cache'));
                //$sliderKz    = new SourceSliderKz($apiSliderKz);

                if (file_exists(LOCAL_FILES_DIR)) {
                    $localFiles = new SourceLocalFiles(LOCAL_FILES_DIR, 'http://player-backend.lndo.site:8000/');
                    $aether->addSource($localFiles, 20);
                }

                $aether->addSource($youTube, 1);
                //$aether->addSource($sliderKz, 10);

                return $aether;
                break;
            case 'config':
            case 'configuration':
                return Configurations::singleton();
                break;
        }

        return null;
    }
}
