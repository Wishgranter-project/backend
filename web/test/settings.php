<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;

$currentFile           = Server::getCurrentFile();
$currentDirectory      = File::getParentDirectory($currentFile);
$parentDirectory       = File::getParentDirectory($currentDirectory);
$grandParentDirectory  = File::getParentDirectory($parentDirectory);

//=============================================================================

define('SERVER_ROOT',                  Server::getServerRoot());

$settings['corsAllowedDomain']         = 'wishgranter-frontend.ddev.site';
define('APP_DIR',                      $currentDirectory);
define('PLAYER_FILES_DIR',             File::getParentDirectory(SERVER_ROOT) . 'player-files-test/');






define('LOCAL_MEDIA_DIR',              APP_DIR . 'local-medias/');
define('CACHE_DIR',                    PLAYER_FILES_DIR . 'cache/');
define('PLAYLISTS_DIR',                PLAYER_FILES_DIR . 'playlist/');
define('USERS_DIR',                    PLAYER_FILES_DIR . 'user/');
define('SESSIONS_DIR',                 PLAYER_FILES_DIR . 'session/');
define('PLAYLISTS_DIR_TEST_TEMPLATES', PLAYER_FILES_DIR . 'playlist-templates/');
define('USERS_DIR_TEST_TEMPLATES',     PLAYER_FILES_DIR . 'user-templates/');
