<?php

namespace WishgranterProject\Backend\Server;

use WishgranterProject\Backend\Service\ServiceLocator;
use WishgranterProject\Backend\Server\Server;
use Psr\Http\Message\ServerRequestInterface;

class Bootstrap
{
    protected $serviceLocator;

    public function __construct(protected string $settingsFile)
    {
        $this->serviceLocator = ServiceLocator::singleton();
    }

    public function bootstrap()
    {
        require $this->settingsFile;
        $this->serviceLocator->get('settings')->setSettings($settings);

        if (!file_exists(DIR_APP . 'configurations.json')) {
            copy(DIR_APP . 'configurations.template.json', DIR_APP . 'configurations.json');
        }

        if (!file_exists(DIR_COLLECTIONS)) {
            mkdir(DIR_COLLECTIONS, 0777, true);
        }

        if (!file_exists(DIR_CACHE)) {
            mkdir(DIR_CACHE, 0777, true);
        }

        if (!file_exists(DIR_SESSIONS)) {
            mkdir(DIR_SESSIONS, 0777, true);
        }

        if (!file_exists(DIR_LOCAL_MEDIA)) {
            mkdir(DIR_LOCAL_MEDIA, 0777, true);
        }
    }

    public function getServer()
    {
        return new Server($this->serviceLocator);
    }

    /**
     * Checks weather this is a dev or production environment.
     *
     * @return bool
     *   True if it is.
     */
    public static function isLocalEnvironment(): bool
    {
        return getenv('IS_DDEV_PROJECT') == 'true';
    }
}
