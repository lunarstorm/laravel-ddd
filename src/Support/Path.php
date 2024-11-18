<?php

namespace Lunarstorm\LaravelDDD\Support;

class Path
{
    public static function normalize($path)
    {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }

    public static function join(...$parts)
    {
        $parts = array_map(function ($part) {
            return trim(static::normalize($part), DIRECTORY_SEPARATOR);
        }, $parts);

        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    public static function fromNamespace(string $namespace, ?string $classname = null): string
    {
        return str($namespace)
            ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
            ->when($classname, fn ($s) => $s->append("{$classname}.php"))
            ->toString();
    }

    public static function filePathToNamespace(string $path, string $namespacePath, string $namespace): string
    {
        return str_replace(
            [base_path().'/'.$namespacePath, '/', '.php'],
            [$namespace, '\\', ''],
            $path
        );
    }

    public static function normalizeNamespace(string $namespace): string
    {
        return str_replace(['\\', '/'], '\\', $namespace);
    }
}
