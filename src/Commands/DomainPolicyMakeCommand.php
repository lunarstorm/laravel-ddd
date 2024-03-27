<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainPolicyMakeCommand extends PolicyMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:policy';
}
