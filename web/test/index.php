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

if (!file_exists(PLAYLISTS_DIR)) {
    mkdir(PLAYLISTS_DIR);
}

if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR);
}

if (!file_exists(SESSIONS_DIR)) {
    mkdir(SESSIONS_DIR);
}

if (!file_exists(LOCAL_FILES_DIR)) {
    mkdir(LOCAL_FILES_DIR);
}

//=============================================================================

function scan_dir($directory): array
{
    $entries = array_slice(scandir($directory), 2);
    array_walk($entries, function (&$entry) use ($directory) {
        $entry = $directory . $entry;
    });

    return $entries;
}

// Reset the test playlist files at every request.
function copy_files($fromDir, $toDir)
{
    $entries = scan_dir($fromDir);
    foreach ($entries as $entry) {
        if (is_dir($entry)) {
            $entry .= '/';
            $destination = $toDir . basename($entry) . '/';
            if (!file_exists($destination)) {
                mkdir($destination);
            }
            copy_files($entry, $destination);
        } else {
            $destination = $toDir . basename($entry);
            if (file_exists($destination)) {
                unlink($destination);
            }
            copy($entry, $destination);
        }
    }
}

copy_files(PLAYLISTS_DIR_TEST_TEMPLATES, PLAYLISTS_DIR);

//=============================================================================

$server = new Server();

/** @var AdinanCenci\Router\Router */
$router = $server->getRouter(ROOT_DIR . 'routes.php');

$router->run();
