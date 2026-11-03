<?php

namespace WishgranterProject\Backend\Token;

use WishgranterProject\Backend\User\UserInterface;
use WishgranterProject\Backend\ExpirableCredential\ExpirableCredential;

class Token extends ExpirableCredential implements TokenInterface
{
    /**
     * Constructor.
     *
     * @param string $filename
     *   Absolute filename that stores the grant.
     * @param null|WishgranterProject\Backend\User\UserInterface
     *   The user that owns the grant and gets to use it.
     * @param int $created
     *   Timestamp of when the grant was created.
     * @param int $expiration
     *   Expiration timestamp.
     * @param int $action
     *   The action the token grants.
     */
    public function __construct(
        protected string $filename,
        protected ?UserInterface $user,
        protected int $created,
        protected int $expiration,
        protected string $action,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function dataTransfer(): array
    {
        $data = parent::dataTransfer();
        $data['action'] = $this->action;

        return $data;
    }
}
