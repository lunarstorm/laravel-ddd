<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ScopeMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainScopeMakeCommand extends ScopeMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:scope';
}
