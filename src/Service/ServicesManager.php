<?php 
namespace AdinanCenci\Player\Service;

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
            case 'discogs':
                return Discogs::create();
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
