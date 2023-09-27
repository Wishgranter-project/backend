<?php 
use AdinanCenci\Player\Helper\JsonResource;

$router->before('*', '#^.*$#', function($request, $handler) 
{
    if (defined('PLAYLISTS_DIR') && !file_exists(PLAYLISTS_DIR)) {
        mkdir(PLAYLISTS_DIR);
    }

    if (defined('CACHE_DIR') && !file_exists(CACHE_DIR)) {
        mkdir(CACHE_DIR);
    }
});



/** CORS Pre-flight */
$router->options('#^.*$#', function($request, $handler) 
{
    $response = $handler->responseFactory->ok('');
    $response = $response->withAddedHeader('Access-Control-Allow-Origin', getAllowedDomain($request, $GLOBALS['settings']));
    $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
    $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'content-type');
    $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
    return $response;
});

// Add CORS headers to all responses.
$router->before('GET|POST|PUT|PATCH|DELETE', '#^.*$#', function($request, $handler) 
{
    $response = $handler->handle($request);
    if (!$response->hasHeader('Access-Control-Allow-Origin')) {
        $response = $response->withAddedHeader('Access-Control-Allow-Origin', getAllowedDomain($request, $GLOBALS['settings']));
        $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
        $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'content-type');
        $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
    }
    return $response;
});

$router->get('#^$#', function($request, $handler) 
{
    echo 'back end home page';
});

// COLLECTION
$router->setDefaultNamespace('\\AdinanCenci\\Player\\Controller');
$router->get(   '#api/v1/collection/artists/?$#',                              'ArtistsList');

// PLAYLIST
$router->post(  '#api/v1/collection/playlists/?$#',                            'PlaylistCreate');
$router->get(   '#api/v1/collection/playlists/?$#',                            'PlaylistReadList');
$router->get(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistRead');
$router->get(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)/items/?$#',  'PlaylistReadItems');
$router->put(   '#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistUpdate');
$router->delete('#api/v1/collection/playlists/(?<playlist>[\w-]+)$#',          'PlaylistDelete');


$router->post(  '#api/v1/collection/items/?$#',                                'ItemCreate');
$router->get(   '#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemRead');
$router->get(   '#api/v1/collection/items/?$#',                                'ItemSearch');
$router->put(   '#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemUpdate');
$router->delete('#api/v1/collection/items/(?<itemUuid>[\w-]+)/?$#',            'ItemDelete');


// DISCOVER
$router->get(   '#api/v1/discover/artists$#',                                  'DiscoverArtists');
$router->get(   '#api/v1/discover/releases$#',                                 'DiscoverReleases');
$router->get(   '#api/v1/discover/releases/(?<releaseId>.+)$#',                'ReleaseRead');
$router->get(   '#api/v1/discover/resources$#',                                'DiscoverResources');

// ERRORS
$router->setNotFoundHandler(function($request, $handler, $path) 
{
    $resource = new JsonResource();
    return $resource
        ->setStatusCode(404)
        ->addError(404, 'nothing found related to ' . $path)
        ->renderResponse();
});

$router->setExceptionHandler(function($request, $handler, $path, $exception) 
{
    $resource = new JsonResource();
    return $resource
        ->setStatusCode(500)
        ->addError(500, $exception->getMessage())
        ->renderResponse();
});
