<?php
namespace AdinanCenci\Player\Service;

class YoutubeApi 
{
    protected string $apiKey;

    public function __construct(string $apiKey) 
    {
        $this->apiKey = $apiKey;
    }

    public static function create() : YoutubeApi
    {
        return new self('AIzaSyCHM5UA_kD9Bq-pONXOuAIQlBCOAWWRR18');
    }

    /**
     * @param string $query
     *
     * @return array
     */
    public function search(string $query) : array
    {
        $body  = $this->request($query);
        $data  = json_decode($body, true);

        return $data;
    }

    protected function request(string $query) : string
    {
        $url = 'https://youtube.googleapis.com/youtube/v3/search?type=video&part=snippet&videoEmbeddable=true&q=' . urlencode($query) . '&key=' . $this->apiKey;

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
