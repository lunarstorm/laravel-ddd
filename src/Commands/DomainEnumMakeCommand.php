<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\EnumMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainEnumMakeCommand extends EnumMakeCommand
{
    use ResolvesDomainFromInput;
}
