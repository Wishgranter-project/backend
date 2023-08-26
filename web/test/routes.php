<?php 

$router->before('*', '#^.*$#', function($request, $handler) 
{
    if (!file_exists(PLAYLISTS_DIR_TEST)) {
        mkdir(PLAYLISTS_DIR_TEST);
    }

    if (!file_exists(CACHE_DIR_TEST)) {
        mkdir(CACHE_DIR_TEST);
    }

    foreach (scandir(PLAYLISTS_DIR_TEST) as $entry) {
        if (is_file(PLAYLISTS_DIR_TEST . $entry)) {
            unlink(PLAYLISTS_DIR_TEST . $entry);
        }
    }

    foreach (scandir(PLAYLISTS_DIR_TEST_TEMPLATES) as $entry) {
        if (is_file(PLAYLISTS_DIR_TEST_TEMPLATES . $entry)) {
            copy(PLAYLISTS_DIR_TEST_TEMPLATES . $entry, PLAYLISTS_DIR_TEST . $entry);
        }
    }
});
