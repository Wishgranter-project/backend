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
define('FILES_DIR',                    $grandParentDirectory . 'files/');
define('LOCAL_FILES_DIR',              FILES_DIR . 'local-files/');
define('CACHE_DIR_TEST',               FILES_DIR . 'cache-test/');
define('PLAYLISTS_DIR_TEST',           FILES_DIR . 'playlist-files-test/');
define('PLAYLISTS_DIR_TEST_TEMPLATES', FILES_DIR . 'playlist-files-test-templates/');
