<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Lorisleiva\Lody\Lody;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class DomainMigration
{
    public static function domainMigrationFolder(): string
    {
        return Path::normalize(config('ddd.namespaces.migration', 'Database/Migrations'));
    }

    public static function cachePaths(): void
    {
        DomainCache::set('domain-migration-folders', static::discoverPaths());
    }

    public static function clearCache(): void
    {
        DomainCache::forget('domain-migration-folders');
    }

    public static function paths(): array
    {
        return DomainCache::has('domain-migration-folders')
            ? DomainCache::get('domain-migration-folders')
            : static::discoverPaths();
    }

    protected static function normalizePaths($path): array
    {
        return collect($path)
            ->filter(fn($path) => is_dir($path))
            ->toArray();
    }

    public static function discoverPaths(): array
    {
        $configValue = config('ddd.autoload.migrations', true);

        if ($configValue === false) {
            return [];
        }

        $paths = static::normalizePaths([
            app()->basePath(DomainResolver::domainPath()),
        ]);

        if (empty($paths)) {
            return [];
        }

        $finder = static::finder($paths);

        return Lody::filesFromFinder($finder)
            ->map(fn($file) => $file->getPath())
            ->unique()
            ->values()
            ->toArray();
    }

    protected static function finder(array $paths)
    {
        $filter = function (SplFileInfo $file) {
            $configuredMigrationFolder = static::domainMigrationFolder();

            $relativePath = Path::normalize($file->getRelativePath());

            return Str::endsWith($relativePath, $configuredMigrationFolder);
        };

        return Finder::create()
            ->files()
            ->in($paths)
            ->filter($filter);
    }
}
