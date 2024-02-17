<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Checks weather this is a dev or production environment.
 *
 * @return bool
 */
function isLocalEnvironment(): bool
{
    return isset($_SERVER['HTTP_X_LANDO']);
}

/**
 * Return CORS allowed domains.
 *
 * @param Psr\Http\Message\ServerRequestInterface $request
 * @param array $settings
 *
 * @return string
 */
function getCorsAllowedDomain(ServerRequestInterface $request, array $settings): string
{
    $host = (string) $request->getHeaderLine('origin');

    $allowedDomain = substr_count($host, $settings['corsAllowedDomain']) > 0
        ? $host
        : 'self';

    return $allowedDomain;
}
