<?php

namespace WishgranterProject\Backend\ExpirableCredential;

use WishgranterProject\Backend\User\UserManager;
use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

abstract class ExpirableCredentialManager
{
    /**
     * Constructor.
     *
     * @param string $directory
     *   Absolute file where the credential files are to be stored.
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
     * @param string $credentialId
     *   The Id.
     *
     * @return null|WishgranterProject\Backend\ExpirableCredential\ExpirableCredentialInterface
     *   The credential object.
     */
    public function getCredential(string $credentialId): ?ExpirableCredentialInterface
    {
        if (!$this->credentialExists($credentialId)) {
            return null;
        }

        $filename = $this->getFilename($credentialId);
        return $this->getCredentialFromFile($filename);
    }

    /**
     * Checks if a specific credential exists.
     *
     * @param string $credentialId
     *   The Id.
     *
     * @return bool
     *   True if it exists.
     */
    public function credentialExists(string $credentialId): bool
    {
        $filename = $this->getFilename($credentialId);
        return file_exists($filename);
    }

    /**
     * Return all credentials to iterate through them.
     */
    public function getCredentials()
    {
        $files = $this->scandir();

        foreach ($files as $file) {
            yield $this->getCredentialFromFile($file);
        }
    }

    /**
     * Clean up expired credentials.
     */
    public function cleanUp()
    {
        foreach ($this->getCredentials() as $grant) {
            if ($grant->isExpired()) {
                $grant->delete();
            }
        }
    }

    /**
     * Scans the directory for credential files.
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
     * Instantiates a credential object from a file.
     *
     * @param string $filename
     *   The file containing the credential.
     *
     * @return WishgranterProject\Backend\ExpirableCredential\ExpirableCredentialInterface
     *   The credential object.
     */
    protected function getCredentialFromFile(string $filename): ExpirableCredentialInterface
    {
        $contents = file_get_contents($filename);
        $data     = json_decode($contents);

        $grant = new ExpirableCredential(
            $filename,
            $this->userManager->getUser($data->userId),
            $data->created,
            $data->expiration,
        );

        return $grant;
    }

    /**
     * Given an id, retrieves the filename containing the credential's data.
     *
     * @param string $credentialId
     *   Grant id.
     *
     * @return string
     *   Absolute filename.
     */
    protected function getFilename(string $credentialId): string
    {
        return $this->directory . '/' . $credentialId . '.json';
    }
}
