<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\InterfaceMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainInterfaceMakeCommand extends InterfaceMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:interface';
}
