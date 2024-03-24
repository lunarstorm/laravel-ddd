<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainProviderMakeCommand extends ProviderMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:provider';
}
