<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainPolicyMakeCommand extends PolicyMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:policy';
}
