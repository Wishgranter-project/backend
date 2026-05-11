<?php

namespace WishgranterProject\Backend\Access;

abstract class AccessResult implements AccessResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function allowed(): bool
    {
        return false;
    }
}
