<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainConsoleMakeCommand extends ConsoleMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:command';
}
