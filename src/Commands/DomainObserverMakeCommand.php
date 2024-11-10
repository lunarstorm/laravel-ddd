<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainObserverMakeCommand extends ObserverMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:observer';
}
