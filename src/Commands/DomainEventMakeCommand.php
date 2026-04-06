<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\EventMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainEventMakeCommand extends EventMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:event';
}
