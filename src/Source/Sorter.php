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
            $this->title = $parameters['title'];
        }

        if (!empty($parameters['soundtrack'])) {
            $this->soundtrack = (array) $parameters['soundtrack'];
        }

        if (!empty($parameters['artist'])) {
            $this->artist = (array) $parameters['artist'];
        }
    }

    public function compare(Resource $resource1, Resource $resource2) : int 
    {
        $score1 = 0;
        $score2 = 0;

        if ($resource1->id == $resource2->id) {
            return $score1 = $score2 = 0;
        }

        $score1 = $this->scoreLikeness($resource1->title);
        $score2 = $this->scoreLikeness($resource2->title);

        if ($score1 == $score2) {
            return 0;
        }

        return $score1 > $score2
            ? -1
            :  1;
    }

    protected function scoreLikeness(string $resourceTitle) : int
    {
        $comparer = new Comparer($resourceTitle, $this->title, $this->artist, $this->sountrack);
        return $comparer->getScore();
    }
}
