<?php
namespace AdinanCenci\Player\Service;

interface SourceInterface 
{
    /**
     * @param string $query
     * @return array
     *
     *  [
     *    [
     *      'id'        => 'service_id:vendor_id',
     *      'title'     => '...',
     *      'thumbnail' => '...',
     *    ]
     * ]
     */
    public function search(array $parameters) : array;
}
