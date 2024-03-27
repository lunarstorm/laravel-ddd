<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ChannelMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainChannelMakeCommand extends ChannelMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:channel';
}
