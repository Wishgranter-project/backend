<?php 
namespace AdinanCenci\Player\Discography;

class Release 
{
    protected $title;

    protected $thumbnail;

    protected $source;

    protected $id;

    protected $tracks;

    public function __construct(string $source, string $id, string $title, ?string $artist, ?int $year = null, ?string $thumbnail = null, array $tracks = []) 
    {
        $this->source    = $source;
        $this->id        = $id;
        $this->title     = $title;
        $this->artist    = $artist;
        $this->year      = $year;
        $this->thumbnail = $thumbnail;
        $this->tracks    = $tracks;
    }

    public function toArray() : array
    {
        $array = [];

        if (!empty($this->source)) {
            $array['source'] = $this->source;
        }

        if (!empty($this->id)) {
            $array['id'] = $this->id;
        }

        if (!empty($this->title)) {
            $array['title'] = $this->title;
        }

        if (!empty($this->artist)) {
            $array['artist'] = $this->artist;
        }

        if (!empty($this->year)) {
            $array['year'] = $this->year;
        }

        if (!empty($this->thumbnail)) {
            $array['thumbnail'] = $this->thumbnail;
        }

        if (!empty($this->tracks)) {
            $array['tracks'] = $this->tracks;
        }

        return $array;
    }

    /**
     * @param string[] $array
     *
     * @return Release
     */
    public static function createFromArray(array $array) : Release
    {
        return new self(
            !empty($array['source']) ? $array['source'] : '',
            !empty($array['id']) ? $array['id'] : '',
            !empty($array['title']) ? $array['title'] : '',
            !empty($array['artist']) ? $array['artist'] : '',
            !empty($array['year']) ? $array['year'] : 0,
            !empty($array['thumbnail']) ? $array['thumbnail'] : '',
            !empty($array['tracks']) ? $array['tracks'] : []
        );
    }
}
