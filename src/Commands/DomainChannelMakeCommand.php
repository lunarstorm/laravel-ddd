<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ChannelMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainChannelMakeCommand extends ChannelMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:channel';
}
