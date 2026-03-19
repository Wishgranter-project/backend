<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;

$currentFile      = Server::getCurrentFile();
$currentDirectory = File::getParentDirectory($currentFile);
$parentDirectory  = File::getParentDirectory($currentDirectory);
$grandParentDirectory  = File::getParentDirectory($parentDirectory);

//=============================================================================

$settings['corsAllowedDomain'] = isLocalEnvironment()
    ? 'wishgranter-frontend.ddev.site'
    : 'adinancenci.com.br';

define('ROOT_DIR',                     $parentDirectory);
define('FILES_DIR',                    $grandParentDirectory . 'files/');
define('LOCAL_FILES_DIR',              FILES_DIR . 'local-medias-test/');
define('CACHE_DIR',                    FILES_DIR . 'cache-test/');
define('PLAYLISTS_DIR',                FILES_DIR . 'playlist-test/');
define('USERS_DIR',                    FILES_DIR . 'user-test/');
define('SESSIONS_DIR',                 FILES_DIR . 'session-test/');

define('PLAYLISTS_DIR_TEST_TEMPLATES', FILES_DIR . 'playlist-test-templates/');
