<?php
namespace AdinanCenci\Player\Source;

abstract class SourceAbstract 
{
    protected function buildQuery(array $parameters) : string
    {
        $query = $parameters['title'];

        if (isset($parameters['artist'])) {
            $query .= ' ' . (is_array($parameters['artist']) ? $parameters['artist'][0] : $parameters['artist']);
        }

        if (isset($parameters['soundtrack'])) {
            $query .= ' ' . $parameters['soundtrack'];
        }

        return $query;
    }
}
