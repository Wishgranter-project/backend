<?php 
namespace AdinanCenci\Player\Service;

class Discogs 
{
    protected string $token;

    public function __construct(string $token) 
    {
        $this->token = $token;
    }

    public function searchForArtist(string $artistName) : \stdClass
    {
        return $this->getJson('database/search?type=artist&title=' . $artistName);
    }

    public function getRelease(string $releaseId) : \stdClass
    {
        return $this->getJson('masters/' . $releaseId);
    }

    public function getArtistAlbums(string $artistName, $page = 1) : \stdClass
    {
        return $this->getJson('database/search?type=master&artist=' . $artistName . '&page=' . $page);
    }

    public static function create() 
    {
        return new self('gcJQjhfYtloEsNKzFnbHlpktYxIheOlWmWRBGWTB');
    }

    protected function getJson(string $url) : \stdClass
    {
        $url = 'https://api.discogs.com/' . $url;
        $json = $this->request($url);
        return json_decode($json);
    }

    protected function request(string $url) 
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
