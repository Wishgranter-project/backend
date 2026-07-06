<?php

namespace WishgranterProject\Backend\Access;

class AccessResultGranted extends AccessResult
{
    /**
     * {@inheritdoc}
     */
    public function isAllowed(): bool
    {
        return true;
    }
}
