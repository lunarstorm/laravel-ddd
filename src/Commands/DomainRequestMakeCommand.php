<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

class DomainRequestMakeCommand extends RequestMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:request';

    protected function rootNamespace()
    {
        return Str::finish(DomainResolver::resolveRootNamespace($this->blueprint->type), '\\');
    }
}
