<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainObserverMakeCommand extends ObserverMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:observer';
}
