<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;
use WishgranterProject\Backend\Server\Bootstrap;

$currentDirectory = File::getParentDirectory(Server::getCurrentFile());

//=============================================================================

define('IS_TEST_ENVIRONMENT',            Bootstrap::isLocalEnvironment());
define('DIR_PUBLIC',                     Server::getServerRoot());
define('DIR_APP',                        $currentDirectory);
define('DIR_PRIVATE',                    File::getParentDirectory(DIR_PUBLIC) . 'private/');
define('DIR_LOCAL_MEDIA',                DIR_APP . 'local-medias/');
define('DIR_PLAYER_FILES',               DIR_PRIVATE . 'player-files-test/');
define('DIR_CACHE',                      DIR_PLAYER_FILES . 'cache/');
define('DIR_COLLECTIONS',                DIR_PLAYER_FILES . 'collection/');
define('DIR_USERS',                      DIR_PLAYER_FILES . 'user/');
define('DIR_SESSIONS',                   DIR_PLAYER_FILES . 'session/');
define('DIR_TEST_COLLECTIONS_TEMPLATES', DIR_PLAYER_FILES . 'collection-templates/');
define('DIR_TEST_USERS_TEMPLATES',       DIR_PLAYER_FILES . 'user-templates/');

$settings['corsAllowedDomain']           = 'wishgranter-frontend.ddev.site';