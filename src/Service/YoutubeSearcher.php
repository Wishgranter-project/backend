<?php
namespace AdinanCenci\Player\Service;

class YoutubeSearcher 
{
    protected string $apiKey;

    public function __construct(string $apiKey) 
    {
        $this->apiKey = $apiKey;
    }

    public static function create() 
    {
        return new self('AIzaSyCHM5UA_kD9Bq-pONXOuAIQlBCOAWWRR18');
    }

    public function search(string $query) 
    {
        $body = $this->request($query);
        $data = json_decode($body, true);

        $ids = [];
        foreach ($data['items'] as $item) {
            if ($item['id']['kind'] == 'youtube#video') {
                $ids[] = $item['id']['videoId'];
            }
        }

        return $ids;
    }

    protected function request(string $query) 
    {
        $url = 'https://youtube.googleapis.com/youtube/v3/search?type=video&videoEmbeddable=true&q=' . urlencode($query) . '&key=' . $this->apiKey;

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
