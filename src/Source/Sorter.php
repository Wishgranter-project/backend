<?php
namespace AdinanCenci\Player\Source;

class Sorter 
{
    protected string $title = '';
    protected array $artist = [];
    protected array $sountrack = [];

    public function __construct($parameters) 
    {
        if (!empty($parameters['title'])) {
            $this->title = $this->normalizeString($parameters['title']);
        }

        if (!empty($parameters['soundtrack'])) {
            $this->soundtrack = (array) $parameters['soundtrack'];
            array_walk($this->soundtrack, [$this, 'normalizeString']);
        }

        if (!empty($parameters['artist'])) {
            $this->artist = (array) $parameters['artist'];
            array_walk($this->artist, [$this, 'normalizeString']);
        }
    }

    public function compare($resource1, $resource2) : int 
    {
        $score1 = 0;
        $score2 = 0;

        if ($resource1->id == $resource2->id) {
            return $score1 = $score2 = 0;
        }

        $title1  = $this->normalizeString($resource1->title);
        $title2  = $this->normalizeString($resource2->title);

        //------------------------

        $score1 += $this->scoreOnTitle($title1);
        $score2 += $this->scoreOnTitle($title2);

        $score1 += $this->scoreOnArtist($title1);
        $score2 += $this->scoreOnArtist($title2);

        $score1 += $this->scoreOnSoundtrack($title1);
        $score2 += $this->scoreOnSoundtrack($title2);

        return $score1 > $score2
            ? -1
            :  1;
    }

    protected function scoreOnTitle(string $resourceTitle) : int
    {
        return $this->title 
            ? $this->substrCount($resourceTitle, (array) $this->title)
            : 0;
    }

    protected function scoreOnArtist(string $resourceTitle) : int
    {
        return $this->artist 
            ? $this->substrCount($resourceTitle, $this->artist)
            : 0;
    }

    protected function scoreOnSoundtrack(string $resourceTitle) : int
    {
        return $this->sountrack 
            ? $this->substrCount($resourceTitle, $this->sountrack)
            : 0;
    }

    protected function substrCount(string $haystack, array $needles) : int
    {
        $count = 0;
        foreach ($needles as $needle) {
            $count += substr_count($haystack, $needle);
        }
        return $count;
    }

    public function normalizeString(string $string) : string
    {
        $string = str_replace(['the', "'", ''], '', $string);
        $string = strtolower($string);

        return $string;
    }
}
