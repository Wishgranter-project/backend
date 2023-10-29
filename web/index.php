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
define('LOCAL_FILES_DIR', $currentDirectory . 'local-files/');

if (!file_exists(ROOT_DIR . 'configurations.json')) {
    copy(ROOT_DIR . 'configurations.template.json', ROOT_DIR . 'configurations.json');
}

$router = new Router();

require 'settings.php';
require 'routes.php';

$router->run();
