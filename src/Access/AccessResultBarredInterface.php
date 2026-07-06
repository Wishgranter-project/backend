<?php

namespace WishgranterProject\Backend\Access;

interface AccessResultBarredInterface extends AccessResultInterface
{
    /**
     * Returns a human readalbe reason for being denied access.
     *
     * @return string
     *   Human readable string.
     */
    public function getReason(): string;
}
