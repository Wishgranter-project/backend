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
define('FILES_DIR',       $parentDirectory . 'files/');
define('LOCAL_FILES_DIR', FILES_DIR . 'local-files/');
define('CACHE_DIR',       FILES_DIR . 'cache/');
define('PLAYLISTS_DIR',   FILES_DIR . 'playlist-files/');
