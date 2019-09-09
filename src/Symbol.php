<?php

namespace Tonysm\Phredis;

final class Symbol
{
    private static $instances = [];

    public static function OK()
    {
        static::$instances["ok"] = static::$instances["ok"] ?? new Symbol();
        return static::$instances["ok"];
    }

    public static function PONG()
    {
        static::$instances["pong"] = static::$instances["pong"] ?? new Symbol();
        return static::$instances["pong"];
    }
}