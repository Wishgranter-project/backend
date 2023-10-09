<?php 
namespace AdinanCenci\Player\Service;
use AdinanCenci\Player\Discography\DiscographyFinder;
use AdinanCenci\Player\Discography\DiscographyDiscogs;
use AdinanCenci\Player\Discography\DiscographyLastFm;

use AdinanCenci\AetherMusic\Api\ApiYouTube;
use AdinanCenci\AetherMusic\Api\ApiSliderKz;
use AdinanCenci\AetherMusic\Source\SourceYouTube;
use AdinanCenci\AetherMusic\Source\SourceSliderKz;
use AdinanCenci\AetherMusic\Aether;

class ServicesManager 
{
    protected $instances = [];

    protected static $singleInstance = null;

    public function get(string $serviceId) 
    {
        if (! isset($this->instances[$serviceId])) {
            $this->instances[$serviceId] = $this->isntantiate($serviceId);
        }

        if (is_null($this->instances[$serviceId])) {
            throw new \InvalidArgumentException('Service ' . $serviceId . ' not found.');
        }

        return $this->instances[$serviceId];
    }

    protected function isntantiate($serviceId) 
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
                $apiYouTube  = new ApiYouTube('AIzaSyCHM5UA_kD9Bq-pONXOuAIQlBCOAWWRR18', [], $this->get('cache'));
                $youTube     = new SourceYouTube($apiYouTube);

                $apiSliderKz = new ApiSliderKz([], $this->get('cache'));
                $sliderKz    = new SourceSliderKz($apiSliderKz);

                $aether = new Aether();
                $aether->addSource($youTube, 1);
                $aether->addSource($sliderKz, 10);

                return $aether;
                break;
        }

        return null;
    }

    public static function singleton() 
    {
        if (self::$singleInstance == null) {
            self::$singleInstance = new self();
        }

        return self::$singleInstance;
    }
}
