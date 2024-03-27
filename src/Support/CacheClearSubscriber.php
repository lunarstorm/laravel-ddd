<?php

namespace Lunarstorm\LaravelDDD\Support;

use ErrorException;
use Illuminate\Events\Dispatcher;

class CacheClearSubscriber
{
    public function __construct()
    {
    }

    public function handle(): void
    {
        $files = glob(base_path(config('ddd.cache_directory').'/ddd-*.php'));

        foreach ($files as $file) {
            try {
                unlink($file);
            } catch (ErrorException $exception) {
                if (! str_contains($exception->getMessage(), 'No such file or directory')) {
                    dump($exception->getMessage());
                    throw $exception;
                }
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     *
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen('cache:clearing', [$this, 'handle']);
    }
}
