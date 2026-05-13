<?php

namespace WishgranterProject\Backend\Server;

use Psr\Http\Message\ServerRequestInterface;

abstract class TestBootstrap extends Bootstrap
{
    public static function bootstrap(string $settingsFile)
    {
        parent::bootstrap($settingsFile);

        self::copyFiles(PLAYLISTS_DIR_TEST_TEMPLATES, PLAYLISTS_DIR);
        self::copyFiles(USERS_DIR_TEST_TEMPLATES,     USERS_DIR);
    }

    public static function scanDir($directory): array
    {
        $entries = array_slice(scandir($directory), 2);
        array_walk($entries, function (&$entry) use ($directory) {
            $entry = $directory . $entry;
        });

        return $entries;
    }

    // Reset the test playlist files at every request.
    public static function copyFiles($fromDir, $toDir)
    {
        $entries = self::scanDir($fromDir);
        foreach ($entries as $entry) {
            if (is_dir($entry)) {
                $entry .= '/';
                $destination = $toDir . basename($entry) . '/';
                if (!file_exists($destination)) {
                    mkdir($destination);
                }
                self::copyFiles($entry, $destination);
            } else {
                $destination = $toDir . basename($entry);
                if (file_exists($destination)) {
                    unlink($destination);
                }
                copy($entry, $destination);
            }
        }
    }
}
