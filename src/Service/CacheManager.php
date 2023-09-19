<?php 
namespace AdinanCenci\Player\Service;

use AdinanCenci\FileCache\Cache;

class CacheManager extends Cache 
{
    public static function create() : CacheManager
    {
        return defined('CACHE_DIR_TEST')
            ? new self(CACHE_DIR_TEST)
            : new self(CACHE_DIR);
    }
}
