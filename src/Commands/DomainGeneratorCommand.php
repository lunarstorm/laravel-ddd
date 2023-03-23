<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

abstract class DomainGeneratorCommand extends GeneratorCommand
{
    protected function rootNamespace()
    {
        return str($this->getDomainBasePath())
            ->rtrim('/\\')
            ->basename()
            ->toString();
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = $this->getDomain();

        return $rootNamespace . '\\' . $domain . '\\' . $this->getRelativeDomainNamespace();
    }

    abstract protected function getRelativeDomainNamespace(): string;

    protected function getNameInput()
    {
        return Str::studly($this->argument('name'));
    }

    protected function getDomainInput()
    {
        return $this->argument('domain');
    }

    protected function getDomain()
    {
        return str($this->getDomainInput())
            ->trim()
            ->studly()
            ->toString();
    }

    protected function getDomainBasePath()
    {
        return $this->laravel->basePath(config('ddd.paths.domains', 'src/Domains'));
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->getDomainBasePath() . '/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function resolveStubPath($path)
    {
        $path = ltrim($path, '/\\');

        $publishedPath = resource_path('stubs/ddd/' . $path);

        return file_exists($publishedPath)
            ? $publishedPath
            : __DIR__ . DIRECTORY_SEPARATOR . '../../stubs' . DIRECTORY_SEPARATOR . $path;
    }
}
