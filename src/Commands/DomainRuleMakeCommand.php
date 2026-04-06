<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\RuleMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainRuleMakeCommand extends RuleMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:rule';
}
