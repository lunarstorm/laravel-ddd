<?php

namespace Lunarstorm\LaravelDDD\Listeners;

use Illuminate\Events\Dispatcher;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;

class CacheClearSubscriber
{
    public function __construct()
    {
    }

    public function handle(): void
    {
        DomainAutoloader::clearCache();
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen('cache:clearing', [$this, 'handle']);
    }
}
