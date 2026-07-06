<?php

namespace WishgranterProject\Backend\Access;

abstract class AccessResult implements AccessResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAllowed(): bool
    {
        return false;
    }
}
