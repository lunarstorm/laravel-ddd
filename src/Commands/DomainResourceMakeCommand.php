<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ResourceMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainResourceMakeCommand extends ResourceMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:resource';
}
