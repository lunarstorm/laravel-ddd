<?php

namespace Tey\LaravelDDD\Commands;

use Illuminate\Foundation\Console\NotificationMakeCommand;
use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Tey\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainNotificationMakeCommand extends NotificationMakeCommand
{
    use HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:notification';
}
