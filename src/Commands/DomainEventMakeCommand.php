<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\EventMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainEventMakeCommand extends EventMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:event';
}
