<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;

$currentFile      = Server::getCurrentFile();
$currentDirectory = File::getParentDirectory($currentFile);
$parentDirectory  = File::getParentDirectory($currentDirectory);

//=============================================================================

define('SERVER_ROOT',                  Server::getServerRoot());

if (isLocalEnvironment()) {
    $settings['corsAllowedDomain']     = 'wishgranter-frontend.ddev.site';
    define('APP_DIR',                  SERVER_ROOT);
    define('PLAYER_FILES_DIR',         File::getParentDirectory(SERVER_ROOT) . 'player-files/');
} else {
    $settings['corsAllowedDomain']     = 'adinancenci.com.br';
    define('APP_DIR',                  SERVER_ROOT . 'player-backend/web/');
    define('PLAYER_FILES_DIR',         File::getParentDirectory(SERVER_ROOT) . 'private/player-files/');
}

define('LOCAL_MEDIA_DIR',              APP_DIR . 'local-medias/');
define('CACHE_DIR',                    PLAYER_FILES_DIR . 'cache/');
define('PLAYLISTS_DIR',                PLAYER_FILES_DIR . 'playlist/');
define('USERS_DIR',                    PLAYER_FILES_DIR . 'user/');
define('SESSIONS_DIR',                 PLAYER_FILES_DIR . 'session/');
