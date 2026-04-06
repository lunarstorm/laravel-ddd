<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainObserverMakeCommand extends ObserverMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:observer';
}
