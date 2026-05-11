<?php

namespace WishgranterProject\Backend\Access;

class AccessResultGranted extends AccessResult
{
    /**
     * {@inheritdoc}
     */
    public function allowed(): bool
    {
        return true;
    }
}
