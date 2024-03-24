<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainConsoleMakeCommand extends ConsoleMakeCommand
{
    use ResolvesDomainFromInput;
}
