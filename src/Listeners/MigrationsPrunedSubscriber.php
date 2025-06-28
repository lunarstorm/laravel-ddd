<?php

namespace Lunarstorm\LaravelDDD\Listeners;

use Illuminate\Database\Events\MigrationsPruned;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SplFileInfo;

class MigrationsPrunedSubscriber
{
    public function __construct() {}

    public function handle(): void
    {
        $locations = [
            config('ddd.domain_path'),
            ...config('ddd.layers'),
        ];

        $migrationDirs = collect();
        /** @var Collection<int, SplFileInfo> $filesToDelete */
        $filesToDelete = collect();

        $filesystem = new Filesystem;

        foreach ($locations as $location) {
            if (! $filesystem->exists($location)) {
                continue;
            }

            $allFiles = $filesystem->allFiles($location);

            foreach ($allFiles as $file) {
                if (
                    Str::endsWith($file->getPath(), 'Database/Migrations')
                    &&
                    $file->getExtension() == 'php'
                ) {
                    $filesToDelete->push($file);
                }
            }
        }

        /** @var Collection<string, Collection<int, SplFileInfo>> $groupedFilesToDelete */
        $groupedFilesToDelete = $filesToDelete->groupBy(function (SplFileInfo $file) {
            return $file->getPath();
        });

        foreach ($groupedFilesToDelete as $location => $groupOfFilesToDelete) {
            foreach ($groupOfFilesToDelete as $file) {
                $filesystem->delete($file->getPathname());
            }

            if ($filesystem->isEmptyDirectory($location)) {
                $filesystem->deleteDirectory($location);
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
