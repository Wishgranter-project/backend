<?php
namespace AdinanCenci\Player\Service;

class SourceSliderKz implements SourceInterface 
{
    public static function create() : SourceSliderKz
    {
        return new self();
    }

    public function search(array $parameters) : array
    {
        $query = $this->buildQuery($parameters);
        $body  = $this->request($query);
        $data  = json_decode($body, true);

        $resources = [];
        foreach ($data['audios'][''] as $audio) {
            $resources[] = new Resource(
                'slider_kz',
                'slider_kz:' . $audio['id'],
                $audio['tit_art'],
                '',
                '',
                $audio['url']
            );
        }

        return $resources;
    }

    protected function request(string $query) : string
    {
        $url = 'https://slider.kz/vk_auth.php?q=' . urlencode($query);

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

        $_SESSION['lastQuery'] = $query;

        return $response;
    }

    protected function buildQuery(array $parameters) : string
    {
        $query = $parameters['title'];

        if (isset($parameters['artist'])) {
            $query .= ' ' . $parameters['artist'];
        }

        if (isset($parameters['soundtrack'])) {
            $query .= ' ' . $parameters['soundtrack'];
        }

        return $query;
    }
}
