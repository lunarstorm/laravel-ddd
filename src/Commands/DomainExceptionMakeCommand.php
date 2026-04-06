<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ExceptionMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainExceptionMakeCommand extends ExceptionMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:exception';
}
