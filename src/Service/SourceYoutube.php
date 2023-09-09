<?php
namespace AdinanCenci\Player\Service;

class SourceYoutube implements SourceInterface 
{
    protected string $apiKey;

    public function __construct(string $apiKey) 
    {
        $this->apiKey = $apiKey;
    }

    public static function create() : SourceYoutube
    {
        return new self('AIzaSyCHM5UA_kD9Bq-pONXOuAIQlBCOAWWRR18');
    }

    public function search(array $parameters) : array
    {
        $query = $this->buildQuery($parameters);
        $body  = $this->request($query);
        $data  = json_decode($body, true);

        $resources = [];
        foreach ($data['items'] as $item) {
            if ($item['id']['kind'] != 'youtube#video') {
                continue;
            }

            $resources[] = new Resource(
                'youtube',
                'youtube:' . $item['id']['videoId'],
                htmlspecialchars_decode($item['snippet']['title']),
                htmlspecialchars_decode($item['snippet']['description']),
                $item['snippet']['thumbnails']['default']['url']
            );
        }

        return $resources;
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
