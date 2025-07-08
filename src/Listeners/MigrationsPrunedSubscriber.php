<?php

namespace Lunarstorm\LaravelDDD\Listeners;

use Illuminate\Database\Events\MigrationsPruned;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Lunarstorm\LaravelDDD\Events\DomainMigrationsPruned;
use Lunarstorm\LaravelDDD\Support\DomainMigration;

class MigrationsPrunedSubscriber
{
    public function handle(MigrationsPruned $event): void
    {
        $migrationDirs = DomainMigration::paths();
        $filesystem = new Filesystem;

        foreach ($migrationDirs as $path) {
            $filesystem->deleteDirectory($path, preserve: false);

            event(new DomainMigrationsPruned($event->connection, $path));
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
