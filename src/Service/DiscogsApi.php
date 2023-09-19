<?php 
namespace AdinanCenci\Player\Service;

use Psr\SimpleCache\CacheInterface;

class DiscogsApi 
{
    protected string $token;
    protected CacheInterface $cache;

    public function __construct(string $token, CacheInterface $cache) 
    {
        $this->token = $token;
        $this->cache = $cache;
    }

    public static function create() : DiscogsApi
    {
        $manager = ServicesManager::singleton();

        return new self(
            'gcJQjhfYtloEsNKzFnbHlpktYxIheOlWmWRBGWTB', 
            $manager->get('cache')
        );
    }

    public function searchForArtist(string $artistName, int $page = 1, int $itensPerPage = 20) : \stdClass
    {
        return $this->getJson('database/search?type=artist&title=' . $artistName . '&page=' . $page . '&per_page=' . $itensPerPage);
    }

    // https://www.discogs.com/developers#page:database,header:database-master-release
    public function getRelease(string $releaseId) : \stdClass
    {
        return $this->getJson('masters/' . $releaseId);
    }

    public function getArtistAlbums(string $artistName, $page = 1) : \stdClass
    {
        return $this->getJson('database/search?type=master&artist=' . $artistName . '&page=' . $page);
    }

    protected function getJson(string $url) : \stdClass
    {
        $cacheKey = md5($url);

        if (!$json = $this->cache->get($cacheKey, false)) {
            $url = 'https://api.discogs.com/' . $url;
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
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Discogs token=' . $this->token,
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'
            ]
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $body;
    }

}
