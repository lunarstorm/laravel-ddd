<?php

namespace Tey\LaravelDDD\Listeners;

use Illuminate\Events\Dispatcher;
use Tey\LaravelDDD\Support\DomainCache;

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
