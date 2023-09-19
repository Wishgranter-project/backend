<?php
namespace AdinanCenci\Player\Service;

use Psr\SimpleCache\CacheInterface;

class YoutubeApi 
{
    protected string $apiKey;
    protected CacheInterface $cache;

    public function __construct(string $apiKey, CacheInterface $cache) 
    {
        $this->apiKey = $apiKey;
        $this->cache = $cache;
    }

    public static function create() : YoutubeApi
    {
        $manager = ServicesManager::singleton();

        return new self(
            'AIzaSyCHM5UA_kD9Bq-pONXOuAIQlBCOAWWRR18', 
            $manager->get('cache')
        );
    }

    /**
     * @param string $query
     *
     * @return \stdClass
     */
    public function searchVideos(string $query) : \stdClass
    {
        return $this->getJson('search?type=video&part=snippet&videoEmbeddable=true&q=' . urlencode($query));
    }

    protected function getJson(string $url) : \stdClass
    {
        $cacheKey = md5($url);

        if (!$json = $this->cache->get($cacheKey, false)) {
            $url = 'https://youtube.googleapis.com/youtube/v3/' . $url . '&key=' . $this->apiKey;
            $json = $this->request($url);
            $this->cache->set($cacheKey, $json, 24 * 60 * 60 * 7);
        }

        return json_decode($json);
    }

    protected function request(string $url) : string 
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
