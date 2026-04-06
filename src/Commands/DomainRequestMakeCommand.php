<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Support\Str;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Tey\LaravelDDD\Support\DomainResolver;

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
