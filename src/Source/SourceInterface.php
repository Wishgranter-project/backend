<?php
namespace AdinanCenci\Player\Source;

interface SourceInterface 
{
    /**
     * @param array $parameters
     *   An associative array with keys describing
     *   a song or album. Example: ['title' =>, 'artist' =>] etc.
     *
     * @return Resource[]
     */
    public function search(array $parameters) : array;
}
