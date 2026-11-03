<?php

namespace WishgranterProject\Backend\Token;

use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\Backend\ExpirableCredential\ExpirableCredentialManager;
use WishgranterProject\Backend\ExpirableCredential\ExpirableCredentialInterface;
use WishgranterProject\DescriptivePlaylist\Utils\Helpers;

class TokenManager extends ExpirableCredentialManager implements TokenManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function issueNewToken(UserInterface $user, string $action, int $expiration): TokenInterface
    {
        $tokenId    = Helpers::guidv4();
        $filename   = $this->getFilename($tokenId);
        $expiration = $expiration
            ? $expiration
            : strtotime('+5 minutes');

        $token = new Token(
            $filename,
            $user,
            time(),
            $expiration,
            $action,
        );

        $token->save();

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(string $tokenId): ?TokenInterface
    {
        return $this->getCredential($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function tokenExists(string $tokenId): bool
    {
        return $this->credentialExists($tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokens()
    {
        return $this->getCredentials();
    }

    /**
     * {@inheritdoc}
     */
    public function validateToken(TokenInterface $token, UserInterface $user, string $action): bool
    {
        return !$token->isExpired() &&
            $token->getUser()->getId() == $user->getId() &&
            $token->getAction() == $action;
    }

    /**
     * {@inheritdoc}
     */
    public function expendToken(TokenInterface $token, UserInterface $user, string $action): bool
    {
        if (!$this->validateToken($token, $user, $action)) {
            return false;
        }

        $token->delete();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCredentialFromFile(string $filename): ExpirableCredentialInterface
    {
        $contents = file_get_contents($filename);
        $data     = json_decode($contents);

        $token = new Token(
            $filename,
            $this->userManager->getUser($data->userId),
            $data->created,
            $data->expiration,
            $data->action,
        );

        return $token;
    }
}
