<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Commands\Concerns\InteractsWithStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

abstract class DomainGeneratorCommand extends GeneratorCommand
{
    use InteractsWithStubs,
        ResolvesDomainFromInput;

    protected function getRelativeDomainNamespace(): string
    {
        return DomainResolver::getRelativeObjectNamespace($this->blueprint->type);
    }

    protected function getNameInput()
    {
        return Str::studly($this->argument('name'));
    }
}
