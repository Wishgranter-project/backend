<?php
namespace AdinanCenci\Player\Source;

class Filter 
{
    protected string $title = '';

    public function __construct($parameters) 
    {
        if (!empty($parameters['title'])) {
            $this->title = $this->normalizeString($parameters['title']);
        }
    }

    public function filter(Resource $resource) : bool
    {
        // Some resources from sliderkz will be relative instead of absolute.
        if ($resource->source && !substr_count($resource->source, 'https:')) {
            return false;
        }

        $resourceTitle = $this->normalizeString($resource->title);

        if ($this->title) {
            return substr_count($resourceTitle, $this->title) > 0;
        }

        return true;
    }

    public function normalizeString(string $string) : string
    {
        $string = trim(strtolower($string));
        $string = str_replace([' '], '', $string);
        return $string;
    }
}
