<?php

/**
 * Beginning.
 */

use WishgranterProject\Backend\Server\Bootstrap;
use AdinanCenci\Router\Helper\Server;

//=============================================================================

if (! file_exists('../vendor/autoload.php')) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die('<h1>Autoload not found</h1>');
}

session_start();
require '../vendor/autoload.php';

//=============================================================================

$bootstrap = new Bootstrap(Server::getServerRoot() . 'settings.php');
$bootstrap->bootstrap();

//=============================================================================

$server = $bootstrap->getServer();
$router = $server->getRouter(DIR_APP . 'routes.php');
$router->run();
