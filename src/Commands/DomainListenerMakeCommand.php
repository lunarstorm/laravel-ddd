<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ListenerMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainListenerMakeCommand extends ListenerMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:listener';
}
