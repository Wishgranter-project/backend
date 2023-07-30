<?php 
namespace AdinanCenci\Player\Service;

use Psr\Http\Message\ResponseInterface;
use AdinanCenci\FileCache\Cache;

class CacheManager 
{
    protected Cache $cache;
    protected string $directory;

    public function __construct($directory = null) 
    {
        $this->directory = CACHE_DIR;

        $this->cache = new Cache($this->directory);
    }

    public static function create() 
    {
        return new self(ROOT_DIR . 'cache/');
    }

    public function get(array $tags, $default = null)
    {
        $cached = $this->cache->get($this->key($tags), $default);

        if ($cached instanceof ResponseInterface) {
            $cached = $cached->withAddedHeader('cache-hit', 'hit');
        }

        return $cached;
    }

    public function set(array $tags, $value)
    {
        return $this->cache->set($this->key($tags), $value);
    }

    public function has($tags) : bool
    {
        return $this->cache->has($this->key($tags));
    }

    public function key(array $tags) : string
    {
        return implode('-', $tags);
    }

    public function invalidateTags($tags) 
    {
        $tags = (array) $tags;
        $allEntries = $this->getCacheItems();
        foreach ($allEntries as $entry) {
            foreach ($tags as $tag) {
                if (substr_count($entry, $tag)) {
                    unlink($this->directory . $entry);
                    break;
                }
            }
        }
    }

    protected function getCacheItems() : array
    {
        $entries = scandir($this->directory);
        return array_filter($entries, function($entry) 
        {
            return !in_array($entry, ['.', '..']);
        });
    }
}
