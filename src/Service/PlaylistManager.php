<?php 
namespace AdinanCenci\Player\Service;

use AdinanCenci\DescriptiveManager\PlaylistManager as BaseManager;

class PlaylistManager extends BaseManager 
{
    public static function create() : self
    {
        return defined('PLAYLISTS_DIR_TEST')
            ? new self(PLAYLISTS_DIR_TEST)
            : new self(PLAYLISTS_DIR);
    }
}
