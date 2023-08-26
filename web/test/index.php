<?php 
use AdinanCenci\Router\Helper\Server;
use AdinanCenci\Router\Helper\File;
use AdinanCenci\Router\Router;

if (! file_exists('../../vendor/autoload.php')) {
    die('<h1>Autoload not found</h1>');
}

require '../../vendor/autoload.php';

$currentFile           = Server::getCurrentFile();
$currentDirectory      = File::getParentDirectory($currentFile);
$parentDirectory       = File::getParentDirectory($currentDirectory);
$grandParentDirectory  = File::getParentDirectory($parentDirectory);

define('ROOT_DIR', $grandParentDirectory);
define('CACHE_DIR_TEST', $grandParentDirectory . 'cache-test/');
define('PLAYLISTS_DIR_TEST', $grandParentDirectory . 'playlist-files-test/');
define('PLAYLISTS_DIR_TEST_TEMPLATES', $grandParentDirectory . 'playlist-files-test-templates/');

$router = new Router();

require '../routes.php';
require 'routes.php';

$router->run();
