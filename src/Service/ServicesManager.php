<?php 
namespace AdinanCenci\Player\Service;
use AdinanCenci\Player\Discography\DiscographyFinder;
use AdinanCenci\Player\Discography\DiscographyDiscogs;
use AdinanCenci\Player\Discography\DiscographyLastFm;

use AdinanCenci\AetherMusic\Api\ApiYouTube;
use AdinanCenci\AetherMusic\Api\ApiSliderKz;
use AdinanCenci\AetherMusic\Source\SourceYouTube;
use AdinanCenci\AetherMusic\Source\SourceSliderKz;
use AdinanCenci\AetherMusic\Source\SourceLocalFiles;
use AdinanCenci\AetherMusic\Aether;

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
                return PlaylistManager::create();
                break;
            case 'cache':
                return CacheManager::create();
                break;
            case 'resourceFinder':
                return ResourceFinder::create();
                break;
            case 'discographyFinder':
                return DiscographyFinder::create();
                break;
            case 'discographyDiscogs':
                return DiscographyDiscogs::create();
                break;
            case 'discographyLastFm':
                return DiscographyLastFm::create();
                break;
            case 'discogsApi':
                return DiscogsApi::create();
                break;
            case 'lastFmApi':
                return LastFmApi::create();
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
