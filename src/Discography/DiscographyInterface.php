<?php 
namespace AdinanCenci\Player\Discography;

use AdinanCenci\Player\Helper\SearchResults;

interface DiscographyInterface 
{
    /**
     * @param string $artistName
     * @param int $page
     * @param int $itensPerPage
     * 
     * @return SearchResults
     */
    public function searchForArtistByName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults;

    /**
     * @param string $artistName
     * @param int $page
     * @param int $itensPerPage
     * 
     * @return Release[]
     */
    public function searchForReleasesByArtistName(string $artistName, int $page = 1, int $itensPerPage = 20) : SearchResults;


    /**
     * @param string $id
     * 
     * @return Release
     */
    public function getReleaseById(string $id) : Release;
}
