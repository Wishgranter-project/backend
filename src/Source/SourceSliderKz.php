<?php
namespace AdinanCenci\Player\Source;

use Psr\SimpleCache\CacheInterface;
use AdinanCenci\Player\Service\ServicesManager;

class SourceSliderKz extends SourceAbstract implements SourceInterface 
{
    protected CacheInterface $cache;

    public function __construct(CacheInterface $cache) 
    {
        $this->cache = $cache;
    }

    public static function create() : SourceSliderKz
    {
        $manager = ServicesManager::singleton();
        return new self($manager->get('cache'));
    }

    public function search(array $parameters) : array
    {
        $query = $this->buildQuery($parameters);
        $data  = $this->getJson('vk_auth.php?q=' . urlencode($query));
        $_SESSION['lastQuery'] = $query;

        $resources = [];
        foreach ($data['audios'][''] as $audio) {
            if (empty($audio)) {
                continue;
            }
            $resources[] = new Resource(
                'slider_kz',
                $audio['id'] . '@slider_kz',
                $audio['tit_art'],
                '',
                '',
                $audio['url']
            );
        }

        return $resources;
    }

    protected function getJson(string $url) : array
    {
        $cacheKey = md5($url);

        if (!$json = $this->cache->get($cacheKey, false)) {
            $url = 'https://slider.kz/' . $url;
            $json = $this->request($url);
            $this->cache->set($cacheKey, $json, 24 * 60 * 60 * 7);
        }

        return json_decode($json, true);
    }

    protected function request(string $url) : string
    {
        $lastQuery = !empty($_SESSION['lastQuery']) ? $_SESSION['lastQuery'] : '';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Accept-Language: en-US,en;q=0.7',
                'Cache-Control: no-cache',
                //'Connection: keep-alive',
                'Host: slider.kz',
                'Pragma: no-cache',
                ($lastQuery ? 'Referer: https://slider.kz/#' . urlencode($lastQuery) : 'Referer: https://slider.kz/'),
                'Sec-Ch-Ua: "Chromium";v="116", "Not)A;Brand";v="24", "Brave";v="116"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Linux"',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'Sec-Gpc: 1',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36',
                'X-Requested-With: XMLHttpRequest'
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
