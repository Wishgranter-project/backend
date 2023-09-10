<?php 
use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;
use AdinanCenci\Router\Router;

session_start();

if (! file_exists('../vendor/autoload.php')) {
    die('<h1>Autoload not found</h1>');
}

require '../vendor/autoload.php';

$currentFile      = Server::getCurrentFile();
$currentDirectory = File::getParentDirectory($currentFile);
$parentDirectory  = File::getParentDirectory($currentDirectory);

define('ROOT_DIR', $currentDirectory);
define('CACHE_DIR', $parentDirectory . 'cache/');
define('PLAYLISTS_DIR', $parentDirectory . 'playlist-files/');

$router = new Router();

require 'routes.php';

$router->run();
