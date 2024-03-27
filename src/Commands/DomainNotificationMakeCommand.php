<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\NotificationMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainNotificationMakeCommand extends NotificationMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:notification';
}
