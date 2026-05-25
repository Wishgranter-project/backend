<?php

use WishgranterProject\Backend\Server\Bootstrap;

//=============================================================================

define('IS_TEST_ENVIRONMENT', Bootstrap::isLocalEnvironment());

if (IS_TEST_ENVIRONMENT) {
    require './settings.dev.php';
} else {
    require './settings.prod.php';
}
