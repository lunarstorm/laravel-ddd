<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ClassMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainClassMakeCommand extends ClassMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:class';
}
