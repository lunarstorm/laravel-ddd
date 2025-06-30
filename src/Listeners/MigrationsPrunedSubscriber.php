<?php

namespace Lunarstorm\LaravelDDD\Listeners;

use Illuminate\Database\Events\MigrationsPruned;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;
use Lorisleiva\Lody\Lody;
use Lunarstorm\LaravelDDD\Support\DomainMigration;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class MigrationsPrunedSubscriber
{
    public function handle(): void
    {
        $migrationDirs = DomainMigration::paths();
        $filesystem = new Filesystem;

        foreach ($migrationDirs as $migrationDir) {
            /** @var LazyCollection<int, SplFileInfo> $filesToDelete */
            $filesToDelete = Lody::filesFromFinder(
                Finder::create()
                    ->files()
                    ->in($migrationDir)
                    ->filter(static fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
            );

            foreach ($filesToDelete as $file) {
                $filesystem->delete($file->getPathname());
            }

            if ($filesystem->isEmptyDirectory($migrationDir)) {
                $filesystem->deleteDirectory($migrationDir);
            }
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
