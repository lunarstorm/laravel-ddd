<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\Support\Path;
use Symfony\Component\Console\Input\InputOption;

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
