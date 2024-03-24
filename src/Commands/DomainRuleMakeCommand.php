<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\RuleMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainRuleMakeCommand extends RuleMakeCommand
{
    use ResolvesDomainFromInput;
}
