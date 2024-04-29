<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

trait ResolvesStubPath
{
    protected function resolveStubPath($path)
    {
        $path = ltrim($path, '/\\');

        $publishedPath = resource_path('stubs/ddd/'.$path);

        return file_exists($publishedPath)
            ? $publishedPath
            : __DIR__.DIRECTORY_SEPARATOR.'../../../stubs'.DIRECTORY_SEPARATOR.$path;
    }
}
