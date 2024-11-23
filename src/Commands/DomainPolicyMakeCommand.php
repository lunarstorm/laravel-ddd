<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainPolicyMakeCommand extends PolicyMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:policy';
}
