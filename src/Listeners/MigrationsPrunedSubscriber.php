<?php

namespace Lunarstorm\LaravelDDD\Listeners;

use Illuminate\Database\Events\MigrationsPruned;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;

class MigrationsPrunedSubscriber
{
    public function __construct() {}

    public function handle(): void
    {
        $locations = [
            config('ddd.domain_path'),
            ...config('ddd.layers')
        ];

        $migrationDirs = collect();

        foreach ($locations as $location) {
            $command = 'find ' . base_path("$location/**/Database") . ' -type d -name "Migrations" 2>/dev/null';
            // 2>/dev/null at the end is to ignore warnings such as: find: /.../src/Infrastructure/**/Database: No such file or directory

            $result = collect(shell_exec(
                $command
            ) ?? []);

            $migrationDirs->push(
                ...explode(PHP_EOL, $result[0] ?? '')
            );
        }

        $migrationDirs = $migrationDirs
            ->map(static fn (string $dir): string => trim($dir))
            ->filter(static fn (string $dir): bool => ! empty($dir))
            ->map(static fn (string $dir): string => str_replace(base_path('/'), '', $dir));

        foreach ($migrationDirs as $dir) {
            // we could delete the directories, but that could cause damage if a bug happened,
            // instead we'll just delete php files and only delete the directory afterwards if it is empty

            $filesystem = new Filesystem;

            $files = $filesystem->files($dir);

            foreach ($files as $file) {
                if ($file->getExtension() == 'php') {
                    $filesystem->delete("$file");
                }
            }

            if ($filesystem->isEmptyDirectory($dir)) {
                $filesystem->deleteDirectory($dir);
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
