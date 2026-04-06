<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Tey\LaravelDDD\Commands\Concerns\InteractsWithStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Tey\LaravelDDD\Support\DomainResolver;

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
