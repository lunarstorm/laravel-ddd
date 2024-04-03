<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ClassMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainClassMakeCommand extends ClassMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:class';
}
