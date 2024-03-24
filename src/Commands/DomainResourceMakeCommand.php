<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ResourceMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainResourceMakeCommand extends ResourceMakeCommand
{
    use ResolvesDomainFromInput;
}
