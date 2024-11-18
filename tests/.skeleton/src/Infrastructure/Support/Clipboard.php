<?php

namespace Infrastructure\Support;

class Clipboard
{
    protected static $clips = [];

    public static function set(string $key, $value): void
    {
        data_set(static::$clips, $key, $value);
    }

    public static function get(string $key)
    {
        return data_get(static::$clips, $key);
    }
}
