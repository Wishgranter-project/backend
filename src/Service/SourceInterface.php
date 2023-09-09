<?php
namespace AdinanCenci\Player\Service;

interface SourceInterface 
{
    /**
     * @param string $query
     * @return Resource[]
     */
    public function search(array $parameters) : array;
}
