<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;

$currentFile      = Server::getCurrentFile();
$currentDirectory = File::getParentDirectory($currentFile);
$parentDirectory  = File::getParentDirectory($currentDirectory);

//=============================================================================

$settings['corsAllowedDomain'] = isLocalEnvironment()
    ? 'player-frontend.lndo.site'
    : 'adinancenci.com.br';

define('ROOT_DIR',        $currentDirectory);
define('CACHE_DIR',       $parentDirectory . 'cache/');
define('PLAYLISTS_DIR',   $parentDirectory . 'playlist-files/');
define('LOCAL_FILES_DIR', $currentDirectory . 'local-files/');
