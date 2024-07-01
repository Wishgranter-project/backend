<?php

namespace WishgranterProject\Backend\Helper;

abstract class Singleton
{
    public static $instances = [];

    public static function singleton()
    {
        $class = get_called_class();

        if (!isset(Singleton::$instances[$class])) {
            Singleton::$instances[$class] = new $class();
        }

        return Singleton::$instances[$class];
    }
}
