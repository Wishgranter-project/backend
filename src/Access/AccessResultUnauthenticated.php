<?php

namespace WishgranterProject\Backend\Access;

/**
 * 401: Unauthenticated.
 */
class AccessResultUnauthenticated extends AccessResultBarred
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        protected string $reason = 'You are unauthenticated.'
    ) {
    }
}
