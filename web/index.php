<?php

use WishgranterProject\Backend\Server;

if (! file_exists('../vendor/autoload.php')) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die('<h1>Autoload not found</h1>');
}

session_start();
require '../vendor/autoload.php';

//=============================================================================

require 'functions.php';
require 'settings.php';

if (!file_exists(ROOT_DIR . 'configurations.json')) {
    copy(ROOT_DIR . 'configurations.template.json', ROOT_DIR . 'configurations.json');
}

if (!file_exists(PLAYLISTS_DIR)) {
    mkdir(PLAYLISTS_DIR);
}

if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR);
}

//=============================================================================

$server = new Server();

/** @var AdinanCenci\Router\Router */
$router = $server->getRouter(ROOT_DIR . 'routes.php');

$router->run();
