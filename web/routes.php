<?php

if (!isset($router)) {
    die();
}

$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller\\HomePage');

$router->get(   '#^$#',                                                        'HomePage');

// COLLECTION
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller\\Collection');
$router->get(   '#api/v1/users/(?<userName>[\w-]+)/collection/artists/?$#',                              'ArtistsList');

// PLAYLIST
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller\\Collection\\Playlist');
$router->post(  '#api/v1/users/(?<userName>[\w-]+)/collection/playlists/?$#',                            'CreatePlaylist');
$router->get(   '#api/v1/users/(?<userName>[\w-]+)/collection/playlists/?$#',                            'ListPlaylists');
$router->get(   '#api/v1/users/(?<userName>[\w-]+)/collection/playlists/(?<playlist>[\w-]+)$#',          'ReadPlaylist');
$router->get(   '#api/v1/users/(?<userName>[\w-]+)/collection/playlists/(?<playlist>[\w-]+)/items/?$#',  'ReadPlaylistItems');
$router->put(   '#api/v1/users/(?<userName>[\w-]+)/collection/playlists/(?<playlist>[\w-]+)$#',          'UpdatePlaylist');
$router->delete('#api/v1/users/(?<userName>[\w-]+)/collection/playlists/(?<playlist>[\w-]+)$#',          'DeletePlaylist');

// ITEMS
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller\\Collection\\Item');
$router->post(  '#api/v1/users/(?<userName>[\w-]+)/collection/items/?$#',                                'CreateItem');
$router->get(   '#api/v1/users/(?<userName>[\w-]+)/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ReadItem');
$router->get(   '#api/v1/users/(?<userName>[\w-]+)/collection/items/?$#',                                'SearchItems');
$router->put(   '#api/v1/users/(?<userName>[\w-]+)/collection/items/(?<itemUuid>[\w-]+)/?$#',            'UpdateItem');
$router->delete('#api/v1/users/(?<userName>[\w-]+)/collection/items/(?<itemUuid>[\w-]+)/?$#',            'DeleteItem');

// DISCOVER
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller\\Discover');
$router->get(   '#api/v1/discover/artists$#',                                  'DiscoverArtists');
$router->get(   '#api/v1/discover/albums$#',                                   'DiscoverAlbums');
$router->get(   '#api/v1/discover/album$#',                                    'DiscoverAlbum');
$router->get(   '#api/v1/discover/resources$#',                                'DiscoverResources');

// USER
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller\\User');
$router->post(  '#api/v1/user/login$#',                                             'Login');
$router->get(   '#api/v1/user/logout$#',                                            'Logout');
$router->get(   '#api/v1/user/session$#',                                           'Session');

// DEBUG
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller\\Debug');
$router->get(   '#debug/php-info$#',                                           'PhpInformation');
