<?php

namespace WishgranterProject\Backend\Server;

use Psr\Http\Message\ServerRequestInterface;

abstract class Bootstrap
{
    public static function bootstrap(string $settingsFile)
    {
        require $settingsFile;

        if (!file_exists(APP_DIR . 'configurations.json')) {
            copy(APP_DIR . 'configurations.template.json', APP_DIR . 'configurations.json');
        }

        if (!file_exists(PLAYLISTS_DIR)) {
            mkdir(PLAYLISTS_DIR);
        }

        if (!file_exists(CACHE_DIR)) {
            mkdir(CACHE_DIR);
        }

        if (!file_exists(SESSIONS_DIR)) {
            mkdir(SESSIONS_DIR);
        }

        if (!file_exists(LOCAL_MEDIA_DIR)) {
            mkdir(LOCAL_MEDIA_DIR);
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
