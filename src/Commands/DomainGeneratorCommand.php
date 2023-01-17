<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

abstract class DomainGeneratorCommand extends GeneratorCommand
{
    protected function rootNamespace()
    {
        return 'Domains';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $domain = $this->getDomain();

        return $rootNamespace . '\\' . $domain . '\\Models';
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
}
