<?php

namespace WishgranterProject\Backend\Access;

abstract class AccessResultDenied implements AccessResultDeniedInterface
{
    /**
     * Constructor.
     *
     * @param string $reason
     *   A human readalbe reason for being denied access.
     */
    public function __construct(protected string $reason)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function allowed(): bool
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
