<?php

namespace WishgranterProject\Backend\Access;

/**
 * Represents the result of access computation.
 */
interface AccessResultInterface
{
    /**
     * Returns the result.
     *
     * @return bool
     *   Yes or no.
     */
    public function allowed(): bool;
}
