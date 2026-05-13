<?php

namespace WishgranterProject\Backend\Server;

use Psr\Http\Message\ServerRequestInterface;

abstract class Bootstrap
{
    public static function bootstrap(string $settingsFile)
    {
        require $settingsFile;

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

    /**
     * Checks weather this is a dev or production environment.
     *
     * @return bool
     */
    public static function isLocalEnvironment(): bool
    {
        return getenv('IS_DDEV_PROJECT') == 'true';
    }

    /**
     * Return CORS allowed domains.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     * @param array $settings
     *
     * @return string
     */
    public static function getCorsAllowedDomain(ServerRequestInterface $request, array $settings): string
    {
        $host = (string) $request->getHeaderLine('origin');

        $allowedDomain = substr_count($host, $settings['corsAllowedDomain']) > 0
            ? $host
            : 'self';

        return $allowedDomain;
    }
}
