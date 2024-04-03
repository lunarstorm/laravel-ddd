<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\TraitMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainTraitMakeCommand extends TraitMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:trait';
}
