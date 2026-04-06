<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ScopeMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainScopeMakeCommand extends ScopeMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:scope';
}
