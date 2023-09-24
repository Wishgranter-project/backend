<?php
namespace AdinanCenci\Player\Source;

class Comparer 
{
    protected string $resourceTitle = '';

    protected string $title         = '';

    protected array $artist         = [];

    protected array $sountrack      = [];

    protected array $undesirables   = ['cover', 'acoustic', 'live', 'demo', 'demotape'];

    public function __construct(string $resourceTitle, string $title, array $artist, array $sountrack) 
    {
        $this->resourceTitle = $this->normalizeString($resourceTitle);

        $this->title         = $this->normalizeString($title);

        $artist = array_map([$this, 'normalizeString'], $artist);
        $this->artist        = $artist;

        $sountrack = array_map([$this, 'normalizeString'], $sountrack);
        $this->sountrack     = $sountrack;

        $this->undesirables  = $this->compileUndesirables($this->undesirables);
    }

    public function getScore() : int 
    {
        $title = $this->scoreOnTitle();
        $artist = $this->scoreOnArtist();
        $soundtrack = $this->scoreOnSoundtrack();
        $undesirables = $this->scoreOnUndesirables();

        return $title + $artist + $soundtrack + $undesirables;
    }

    protected function scoreOnTitle() : int
    {
        if (!$this->title) {
            return 0;
        }

        return $this->substrCount($this->resourceTitle, (array) $this->title) ?: -1;
    }

    protected function scoreOnArtist() : int
    {
        if (!$this->artist) {
            return 0;
        }

        return $this->substrCount($this->resourceTitle, $this->artist) ?: -1;
    }

    protected function scoreOnSoundtrack() : int
    {
        // hummm ... should soundtrack matter more or less if artist has been specified ? ...
        return $this->sountrack 
            ? $this->substrCount($this->resourceTitle, $this->sountrack)
            : 0;
    }

    protected function scoreOnUndesirables() : int 
    {
        return -1 * $this->substrCount($this->resourceTitle, $this->undesirables);
    }

    protected function substrCount(string $haystack, array $needles) : int
    {
        $count = 0;
        foreach ($needles as $needle) {
            $count += substr_count($haystack, $needle);
        }
        return $count;
    }

    /**
     * Remove undesirable if they actually are part of the the descriptors.
     */
    protected function compileUndesirables(array $baseArray) : array
    {
        $newList = [];

        foreach ($baseArray as $undesirable) {
            if ($this->title && $this->substrCount($undesirable, (array) $this->title)) {
                continue;
            }

            if ($this->artist && $this->substrCount($undesirable, $this->artist)) {
                continue;
            }

            if ($this->soundtrack && $this->substrCount($undesirable, $this->soundtrack)) {
                continue;
            }

            $newList[] = $undesirable;
        }

        return $newList;
    }

    public function normalizeString(string $string) : string
    {
        $string = trim(strtolower($string));
        $string = str_replace([' '], '', $string);
        return $string;
    }
}
