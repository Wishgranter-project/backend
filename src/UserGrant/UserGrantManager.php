<?php

namespace WishgranterProject\Backend\UserGrant;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

abstract class UserGrantManager
{
    /**
     * Constructor.
     *
     * @param string $directory
     *   Absolute file where the grant files are to be stored.
     * @param WishgranterProject\Backend\User\UserManager $userManager
     *   User manager service.
     */
    public function __construct(
        protected string $directory,
        protected UserManager $userManager,
    ) {
    }

    /**
     * Given an Id, returns the respective grant.
     *
     * @param string $grantId
     *   The Id.
     *
     * @return null|WishgranterProject\Backend\UserGrant\UserGrantInterface
     *   The grant object.
     */
    public function getGrant(string $grantId): ?UserGrantInterface
    {
        if (!$this->grantExists($grantId)) {
            return null;
        }

        $filename = $this->getFilename($grantId);
        return $this->getGrantFromFile($filename);
    }

    /**
     * Checks if a specific grant exists.
     *
     * @param string $grantId
     *   The Id.
     *
     * @return bool
     *   True if it exists.
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
     * Clean up expired grants.
     */
    public function cleanUp()
    {
        foreach ($this->getGrants() as $grant) {
            if ($grant->isExpired()) {
                $grant->delete();
            }
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
