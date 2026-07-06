<?php

namespace WishgranterProject\Backend\Server;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Bootstrap the test app.
 */
class TestBootstrap extends Bootstrap
{
    /**
     * {@inheritdoce}
     */
    public function bootstrap()
    {
        parent::bootstrap();
        self::emptyDir(DIR_USERS);
        self::copyFiles(DIR_TEST_COLLECTIONS_TEMPLATES, DIR_COLLECTIONS);
        self::copyFiles(DIR_TEST_USERS_TEMPLATES, DIR_USERS);
    }

    /**
     * Scans the contents of a directory.
     *
     * Unlike the native scandir(), it avoids . and .. and returns absolute
     * paths instead of relative ones.
     *
     * @param string $directory
     *   Absolute path to the directory.
     *
     * @return array
     *   The contents of the directory.
     */
    protected static function scanDir(string $directory): array
    {
        $entries = array_slice(scandir($directory), 2);
        array_walk($entries, function (&$entry) use ($directory) {
            $entry = $directory . $entry;
        });

        return $entries;
    }

    /**
     * Empties a directory.
     *
     * @param string $directory
     *   Absolute path to the directory.
     */
    protected static function emptyDir(string $directory)
    {
        $entries = self::scanDir($directory);
        foreach ($entries as $entry) {
            if (is_dir($entry)) {
                self::emptyDir($entry);
            } else {
                unlink($entry);
            }
        }
    }

    /**
     * Recursiverly copies file from a directory to another.
     *
     * Existing files are replaced.
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
