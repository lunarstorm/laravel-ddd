<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\InterfaceMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainInterfaceMakeCommand extends InterfaceMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:interface';
}
