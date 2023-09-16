<?php 
namespace AdinanCenci\Player\Discography;

class Artist 
{
    protected $name;

    protected $thumbnail;

    protected $source;

    protected $id;

    public function __construct(string $source, string $id, string $name, ?string $thumbnail = null) 
    {
        $this->source    = $source;
        $this->id        = $id;
        $this->name      = $name;
        $this->thumbnail = $thumbnail;
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

        if (!empty($this->name)) {
            $array['name'] = $this->name;
        }

        if (!empty($this->thumbnail)) {
            $array['thumbnail'] = $this->thumbnail;
        }

        return $array;
    }

    /**
     * @param string[] $array
     *
     * @return Artist
     */
    public static function createFromArray(array $array) : Artist
    {
        return new self(
            !empty($array['source']) ? $array['source'] : '',
            !empty($array['id']) ? $array['id'] : '',
            !empty($array['name']) ? $array['name'] : '',
            !empty($array['thumbnail']) ? $array['thumbnail'] : ''
        );
    }
}
