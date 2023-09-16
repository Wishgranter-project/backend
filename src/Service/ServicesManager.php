<?php 
namespace AdinanCenci\Player\Service;

use AdinanCenci\Player\Source\SourceYoutube;
use AdinanCenci\Player\Source\SourceSliderKz;
use AdinanCenci\Player\Source\ResourceFinder;

use AdinanCenci\Player\Discography\DiscographyFinder;
use AdinanCenci\Player\Discography\DiscographyDiscogs;

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
            case 'cacheManager':
                return CacheManager::create();
                break;
            case 'sourceYoutube':
                return SourceYoutube::create();
                break;
            case 'sourceSliderKz':
                return SourceSliderKz::create();
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
            case 'discogsApi':
                return DiscogsApi::create();
                break;
            case 'youtubeApi':
                return YoutubeApi::create();
                break;
            case 'describer':
                return Describer::create();
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
