<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\EnumMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainEnumMakeCommand extends EnumMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:enum';
}
