<?php

namespace Lunarstorm\LaravelDDD\Listeners;

use Illuminate\Database\Events\MigrationsPruned;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Lunarstorm\LaravelDDD\Support\DomainMigration;

class MigrationsPrunedSubscriber
{
    public function handle(): void
    {
        $migrationDirs = DomainMigration::paths();
        $filesystem = new Filesystem;

        foreach ($migrationDirs as $migrationDir) {
            $filesystem->deleteDirectory($migrationDir, preserve: false);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(MigrationsPruned::class, [$this, 'handle']);
    }
}
