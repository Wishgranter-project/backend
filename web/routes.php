<?php

$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller');

$router->get(   '#^$#',                                                        'HomePage');

// COLLECTION
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Collection\\Controller');
$router->get(   '#api/v1/collection/artists/?$#',                              'ArtistsList');

// PLAYLIST
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Collection\\Playlist\\Controller');
$router->post(  '#api/v1/collection/playlists/?$#',                            'PlaylistCreate');
$router->get(   '#api/v1/collection/playlists/?$#',                            'PlaylistReadList');
$router->get(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistRead');
$router->get(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)/items/?$#',  'PlaylistReadItems');
$router->put(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistUpdate');
$router->delete('#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistDelete');

// ITEMS
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Collection\\Item\\Controller');
$router->post(  '#api/v1/collection/items/?$#',                                'ItemCreate');
$router->get(   '#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemRead');
$router->get(   '#api/v1/collection/items/?$#',                                'ItemReadSearch');
$router->put(   '#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemUpdate');
$router->delete('#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemDelete');

// DISCOVER
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Discover\\Controller');
$router->get(   '#api/v1/discover/artists$#',                                  'DiscoverArtists');
$router->get(   '#api/v1/discover/albums$#',                                   'DiscoverAlbums');
$router->get(   '#api/v1/discover/album$#',                                    'DiscoverAlbum');
$router->get(   '#api/v1/discover/resources$#',                                'DiscoverResources');
