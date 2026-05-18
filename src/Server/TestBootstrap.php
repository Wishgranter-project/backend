<?php

namespace WishgranterProject\Backend\Server;

use Psr\Http\Message\ServerRequestInterface;

class TestBootstrap extends Bootstrap
{
    /**
     * Bootstrap the test app.
     */
    public function bootstrap()
    {
        parent::bootstrap();
        self::copyFiles(DIR_TEST_COLLECTIONS_TEMPLATES, DIR_COLLECTIONS);
        self::copyFiles(DIR_TEST_USERS_TEMPLATES, DIR_USERS);
    }

    /**
     * Scans the contents of a directory.
     *
     * Unlike scandir(), it avoids . and ..
     * and returns absolute paths instead of relative ones.
     *
     * @param string $directory
     *   Absolute path to the directory.
     *
     * @return array
     *   The contents of the directory.
     */
    protected static function scanDir($directory): array
    {
        $entries = array_slice(scandir($directory), 2);
        array_walk($entries, function (&$entry) use ($directory) {
            $entry = $directory . $entry;
        });

        return $entries;
    }

    /**
     * Recursiverly copies file from a directory to another.
     *
     * @param string $fromDir
     *   The origin directory.
     * @param string $toDir
     *   The destination directory.
     */
    protected static function copyFiles(string $fromDir, string $toDir)
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
