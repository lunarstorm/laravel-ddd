<?php

namespace Lunarstorm\LaravelDDD\Listeners;

use Illuminate\Events\Dispatcher;
use Lunarstorm\LaravelDDD\Support\DomainCache;

class CacheClearSubscriber
{
    public function __construct() {}

    public function handle(): void
    {
        DomainCache::clear();
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen('cache:clearing', [$this, 'handle']);
    }
}
