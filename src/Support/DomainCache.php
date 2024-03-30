<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Support\Facades\File;

class DomainCache
{
    public static function set($key, $value)
    {
        $cacheDirectory = config('ddd.cache_directory', 'bootstrap/cache/ddd');

        $cacheFilePath = base_path("{$cacheDirectory}/ddd-{$key}.php");

        file_put_contents(
            $cacheFilePath,
            '<?php '.PHP_EOL.'return '.var_export($value, true).';'
        );

        return $value;
    }

    public static function get($key)
    {
        $cacheDirectory = config('ddd.cache_directory', 'bootstrap/cache/ddd');

        $cacheFilePath = base_path("{$cacheDirectory}/ddd-{$key}.php");

        return file_exists($cacheFilePath) ? include $cacheFilePath : null;
    }

    public static function has($key)
    {
        $cacheDirectory = config('ddd.cache_directory', 'bootstrap/cache/ddd');

        $cacheFilePath = base_path("{$cacheDirectory}/ddd-{$key}.php");

        return file_exists($cacheFilePath);
    }

    public static function remember($key, callable $callback)
    {
        return static::has($key)
            ? static::get($key)
            : static::set($key, call_user_func($callback));
    }

    public static function forget($key)
    {
        $cacheDirectory = config('ddd.cache_directory', 'bootstrap/cache/ddd');

        $cacheFilePath = base_path("{$cacheDirectory}/ddd-{$key}.php");

        File::delete($cacheFilePath);
    }

    public static function clear()
    {
        $files = glob(base_path(config('ddd.cache_directory').'/ddd-*.php'));

        File::delete($files);
    }
}
