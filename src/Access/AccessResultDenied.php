<?php

namespace WishgranterProject\Backend\Access;

/**
 * 403: Authenticated but lacks permission.
 */
class AccessResultDenied extends AccessResultBarred
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        protected string $reason = 'You lack an unspecified permission.'
    ) {
    }
}
