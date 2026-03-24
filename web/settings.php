<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;

$currentFile      = Server::getCurrentFile();
$currentDirectory = File::getParentDirectory($currentFile);
$parentDirectory  = File::getParentDirectory($currentDirectory);


//=============================================================================

$settings['corsAllowedDomain'] = isLocalEnvironment()
    ? 'wishgranter-frontend.ddev.site'
    : 'adinancenci.com.br';

define('ROOT_DIR',                     $currentDirectory);
define('LOCAL_FILES_DIR',              $currentDirectory . 'local-medias/');
define('FILES_DIR',                    $parentDirectory . 'files/');
define('CACHE_DIR',                    FILES_DIR . 'cache/');
define('PLAYLISTS_DIR',                FILES_DIR . 'playlist/');
define('USERS_DIR',                    FILES_DIR . 'user/');
define('SESSIONS_DIR',                 FILES_DIR . 'session/');
