<?php

namespace WishgranterProject\Backend\Access;

abstract class AccessResultBarred implements AccessResultBarredInterface
{
    /**
     * Constructor.
     *
     * @param string $reason
     *   A human readalbe reason for being denied access.
     */
    public function __construct(protected string $reason = 'Unknown.')
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
