<?php

/**
 * This file is intended for tests.
 */

use WishgranterProject\Backend\Server\Server;
use WishgranterProject\Backend\Server\TestBootstrap;

//=============================================================================

if (! file_exists('../../vendor/autoload.php')) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die('<h1>Autoload not found</h1>');
}

session_start();
require '../../vendor/autoload.php';

//=============================================================================

if (!TestBootstrap::isLocalEnvironment()) {
    die();
}

TestBootstrap::bootstrap('test-settings.php');

//=============================================================================

$server = new Server();
$router = $server->getRouter(DIR_APP . '../routes.php');
$router->run();
