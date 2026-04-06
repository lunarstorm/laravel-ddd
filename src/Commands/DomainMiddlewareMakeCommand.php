<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Routing\Console\MiddlewareMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainMiddlewareMakeCommand extends MiddlewareMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:middleware';
}
