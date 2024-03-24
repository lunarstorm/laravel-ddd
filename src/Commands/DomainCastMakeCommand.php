<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\CastMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainCastMakeCommand extends CastMakeCommand
{
    use ResolvesDomainFromInput;
}
