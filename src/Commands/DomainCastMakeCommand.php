<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\CastMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainCastMakeCommand extends CastMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:cast';
}
