<?php

/**
 * This file is intended for testing.
 */

use WishgranterProject\Backend\Server;

if (! file_exists('../../vendor/autoload.php')) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die('<h1>Autoload not found</h1>');
}

session_start();
require '../../vendor/autoload.php';

//=============================================================================

require '../functions.php';
require 'settings.php';

if (!file_exists(ROOT_DIR . 'configurations.json')) {
    copy(ROOT_DIR . 'configurations.template.json', ROOT_DIR . 'configurations.json');
}

if (!file_exists(PLAYLISTS_DIR_TEST)) {
    mkdir(PLAYLISTS_DIR_TEST);
}

if (!file_exists(CACHE_DIR_TEST)) {
    mkdir(CACHE_DIR_TEST);
}

// Reset the playlist files at every request.
foreach (scandir(PLAYLISTS_DIR_TEST_TEMPLATES) as $entry) {
    if (is_file(PLAYLISTS_DIR_TEST_TEMPLATES . $entry)) {
        copy(PLAYLISTS_DIR_TEST_TEMPLATES . $entry, PLAYLISTS_DIR_TEST . 'adinan/' . $entry);
    }
}

//=============================================================================

$server = new Server();

/** @var AdinanCenci\Router\Router */
$router = $server->getRouter(ROOT_DIR . 'routes.php');

$router->run();
