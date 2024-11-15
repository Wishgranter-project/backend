<?php

$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Controller');

$router->get(   '#^$#',                                                        'HomePage::respond');

// COLLECTION
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Collection\\Controller');
$router->get(   '#api/v1/collection/artists/?$#',                              'ArtistsList::respond');

// PLAYLIST
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Collection\\Playlist\\Controller');
$router->post(  '#api/v1/collection/playlists/?$#',                            'PlaylistCreate::respond');
$router->get(   '#api/v1/collection/playlists/?$#',                            'PlaylistReadList::respond');
$router->get(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistRead::respond');
$router->get(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)/items/?$#',  'PlaylistReadItems::respond');
$router->put(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistUpdate::respond');
$router->delete('#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistDelete::respond');

// ITEMS
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Collection\\Item\\Controller');
$router->post(  '#api/v1/collection/items/?$#',                                'ItemCreate::respond');
$router->get(   '#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemRead::respond');
$router->get(   '#api/v1/collection/items/?$#',                                'ItemReadSearch::respond');
$router->put(   '#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemUpdate::respond');
$router->delete('#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemDelete::respond');

// DISCOVER
$router->setDefaultNamespace('\\WishgranterProject\\Backend\\Discover\\Controller');
$router->get(   '#api/v1/discover/artists$#',                                  'DiscoverArtists::respond');
$router->get(   '#api/v1/discover/albums$#',                                   'DiscoverAlbums::respond');
$router->get(   '#api/v1/discover/album$#',                                    'DiscoverAlbum::respond');
$router->get(   '#api/v1/discover/resources$#',                                'DiscoverResources::respond');
