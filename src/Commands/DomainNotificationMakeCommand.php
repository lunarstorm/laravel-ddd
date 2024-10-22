<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\NotificationMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainNotificationMakeCommand extends NotificationMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:notification';
}
