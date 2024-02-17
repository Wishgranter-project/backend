<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;

$currentFile      = Server::getCurrentFile();
$currentDirectory = File::getParentDirectory($currentFile);
$parentDirectory  = File::getParentDirectory($currentDirectory);
$grandParentDirectory  = File::getParentDirectory($parentDirectory);

//=============================================================================

$settings['corsAllowedDomain'] = isLocalEnvironment()
    ? 'player-frontend.lndo.site'
    : 'adinancenci.com.br';

define('ROOT_DIR',                     $parentDirectory);
define('CACHE_DIR_TEST',               $grandParentDirectory . 'cache-test/');
define('PLAYLISTS_DIR_TEST',           $grandParentDirectory . 'playlist-files-test/');
define('PLAYLISTS_DIR_TEST_TEMPLATES', $grandParentDirectory . 'playlist-files-test-templates/');
define('LOCAL_FILES_DIR',              $grandParentDirectory . 'local-files/');
