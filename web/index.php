<?php

/**
 * Beginning.
 */

use WishgranterProject\Backend\Server\Server;
use WishgranterProject\Backend\Server\Bootstrap;

//=============================================================================

if (! file_exists('../vendor/autoload.php')) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die('<h1>Autoload not found</h1>');
}

session_start();
require '../vendor/autoload.php';

//=============================================================================

Bootstrap::bootstrap('settings.php');

//=============================================================================

$server = new Server();
$router = $server->getRouter(APP_DIR . 'routes.php');
$router->run();
