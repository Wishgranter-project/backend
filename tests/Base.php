<?php

namespace WishgranterProject\Backend\Tests;

use PHPUnit\Framework\TestCase;

abstract class Base extends TestCase
{
    public function getBaseDirectory()
    {
        return rtrim(__DIR__, '/') . '/files/';
    }

    public function getAbsolutePath(string $relativePath): string
    {
        return $this->getBaseDirectory() . $relativePath;
    }

    public function createDirectory(string $relativePath)
    {
        $relativePath = rtrim($relativePath, '/') . '/';

        $dir = $this->getAbsolutePath($relativePath);
        if (file_exists($dir)) {
            return $dir;
        }

        mkdir($dir);
        return $dir;
    }

    protected function emptyDirectory(string $relativePath)
    {
        $dir = $this->createDirectory($relativePath);

        foreach (scandir($dir) as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }

            unlink($dir . $entry);
        }

        return $dir;
    }
}
