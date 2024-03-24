<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ListenerMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainListenerMakeCommand extends ListenerMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:listener';
}
