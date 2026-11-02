<?php

namespace WishgranterProject\Backend\UserGrant;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

abstract class UserGrantManager
{
    public function __construct(
        protected string $directory,
        protected UserManager $userManager,
    ) {
    }

    public function getGrant(string $grantId): ?UserGrantInterface
    {
        if (!$this->grantExists($grantId)) {
            return null;
        }

        $filename = $this->getFilename($grantId);
        return $this->getGrantFromFile($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function grantExists(string $grantId): bool
    {
        $filename = $this->getFilename($grantId);
        return file_exists($filename);
    }

    /**
     * Return all grants to iterate through them.
     */
    public function getGrants()
    {
        $files = $this->scandir();

        foreach ($files as $file) {
            yield $this->getGrantFromFile($file);
        }
    }

    /**
     * Scans the directory for grant files.
     *
     * @return array
     *   Absolute path to the files.
     */
    protected function scandir(): array
    {
        $files = array_slice(scandir($this->directory), 2);
        $dir = $this->directory;

        array_walk($files, function (&$file) use ($dir) {
            $file = $dir . $file;
        });

        return $files;
    }

    /**
     * Instantiates a grant object from a file.
     *
     * @param string $filename
     *   The file containing the grant.
     *
     * @return WishgranterProject\Backend\UserGrant\UserGrantInterface
     *   The grant object.
     */
    protected function getGrantFromFile(string $filename): UserGrantInterface
    {
        $contents = file_get_contents($filename);
        $data     = json_decode($contents);

        $grant = new UserGrant(
            $filename,
            $this->userManager->getUser($data->userId),
            $data->created,
            $data->expiration,
        );

        return $grant;
    }

    /**
     * Given a grant id, retrieves the filename containing the grant's data.
     *
     * @param string $grantId
     *   Grant id.
     *
     * @return string
     *   Absolute filename.
     */
    protected function getFilename(string $grantId): string
    {
        return $this->directory . '/' . $grantId . '.json';
    }
}
