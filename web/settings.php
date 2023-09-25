<?php 
function isLocalEnvironment() : bool 
{
    return isset($_SERVER['HTTP_X_LANDO']);
}

if (isLocalEnvironment()) {
    $settings['corsAllowedDomain'] = 'player-frontend.lndo.site';
} else {
    $settings['corsAllowedDomain'] = 'adinancenci.com.br';
}

function getAllowedDomain($request, $settings) : string
{
    $host = $request->getHeaderLine('origin');

    $allowedDomain = substr_count($host, $settings['corsAllowedDomain']) > 0
        ? $host
        : 'self';

    return $allowedDomain;
}
