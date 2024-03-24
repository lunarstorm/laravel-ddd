<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ExceptionMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainExceptionMakeCommand extends ExceptionMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:exception';
}
