<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\EventMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainEventMakeCommand extends EventMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:event';
}
