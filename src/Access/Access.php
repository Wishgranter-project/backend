<?php

namespace WishgranterProject\Backend\Access;

abstract class Access
{
    public static function granted(): AccessResultInterface
    {
        return new AccessResultGranted();
    }

    public static function denied(string $reason): AccessResultInterface
    {
        return new AccessResultDenied($reason);
    }

    public static function unauthenticated(string $reason): AccessResultInterface
    {
        return new AccessResultUnauthenticated($reason);
    }
}
