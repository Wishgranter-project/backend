<?php

use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;
use WishgranterProject\Backend\Server\Bootstrap;



//=============================================================================

$settings['corsAllowedDomain'] = Bootstrap::isLocalEnvironment()
    ? 'wishgranter-frontend.ddev.site'
    : 'adinancenci.com.br';

define('DIR_PUBLIC',                     Server::getServerRoot());
define('DIR_APP',                        Bootstrap::isLocalEnvironment() ? DIR_PUBLIC : DIR_PUBLIC . 'player-backend/web/');
define('DIR_PRIVATE',                    File::getParentDirectory(DIR_PUBLIC) . 'private/');
define('DIR_PLAYER_FILES',               DIR_PRIVATE . 'player-files/');
define('DIR_LOCAL_MEDIA',                DIR_APP . 'local-medias/');
define('DIR_CACHE',                      DIR_PLAYER_FILES . 'cache/');
define('DIR_COLLECTIONS',                DIR_PLAYER_FILES . 'collection/');
define('DIR_USERS',                      DIR_PLAYER_FILES . 'user/');
define('DIR_SESSIONS',                   DIR_PLAYER_FILES . 'session/');
