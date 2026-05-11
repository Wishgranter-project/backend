<?php

namespace WishgranterProject\Backend\Access;

abstract class Access
{
    public static function granted(): AccessResultInterface
    {
        return new AccessResultGranted();
    }

    public static function unauthorized(string $reason): AccessResultInterface
    {
        return new AccessResultUnauthorized($reason);
    }

    public static function forbidden(string $reason): AccessResultInterface
    {
        return new AccessResultForbidden($reason);
    }
}
