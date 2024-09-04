<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Lorisleiva\Lody\Lody;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class DomainAutoloader
{
    public function __construct()
    {
        //
    }

    public function autoload(): void
    {
        if (! config()->has('ddd.autoload')) {
            return;
        }

        $this->handleProviders();

        if (app()->runningInConsole()) {
            $this->handleCommands();
        }

        if (config('ddd.autoload.policies') === true) {
            $this->handlePolicies();
        }

        if (config('ddd.autoload.factories') === true) {
            $this->handleFactories();
        }
    }

    protected static function normalizePaths($path): array
    {
        return collect($path)
            ->filter(fn ($path) => is_dir($path))
            ->toArray();
    }

    protected function handleProviders(): void
    {
        $providers = DomainCache::has('domain-providers')
            ? DomainCache::get('domain-providers')
            : static::discoverProviders();

        foreach ($providers as $provider) {
            app()->register($provider);
        }
    }

    protected function handleCommands(): void
    {
        $commands = DomainCache::has('domain-commands')
            ? DomainCache::get('domain-commands')
            : static::discoverCommands();

        foreach ($commands as $command) {
            $this->registerCommand($command);
        }
    }

    protected function registerCommand($class)
    {
        ConsoleApplication::starting(function ($artisan) use ($class) {
            $artisan->resolve($class);
        });
    }

    protected function handlePolicies(): void
    {
        Gate::guessPolicyNamesUsing(static function (string $class): array|string {
            if ($model = DomainObject::fromClass($class, 'model')) {
                return (new Domain($model->domain))
                    ->object('policy', "{$model->name}Policy")
                    ->fullyQualifiedName;
            }

            $classDirname = str_replace('/', '\\', dirname(str_replace('\\', '/', $class)));

            $classDirnameSegments = explode('\\', $classDirname);

            return Arr::wrap(Collection::times(count($classDirnameSegments), function ($index) use ($class, $classDirnameSegments) {
                $classDirname = implode('\\', array_slice($classDirnameSegments, 0, $index));

                return $classDirname.'\\Policies\\'.class_basename($class).'Policy';
            })->reverse()->values()->first(function ($class) {
                return class_exists($class);
            }) ?: [$classDirname.'\\Policies\\'.class_basename($class).'Policy']);
        });
    }

    protected function handleFactories(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if ($factoryName = DomainFactory::resolveFactoryName($modelName)) {
                return $factoryName;
            }

            $appNamespace = static::appNamespace();

            $modelName = Str::startsWith($modelName, $appNamespace.'Models\\')
                ? Str::after($modelName, $appNamespace.'Models\\')
                : Str::after($modelName, $appNamespace);

            return 'Database\\Factories\\'.$modelName.'Factory';
        });
    }

    protected static function finder($paths)
    {
        $filter = app('ddd')->getAutoloadFilter() ?? function (SplFileInfo $file) {
            $pathAfterDomain = str($file->getRelativePath())
                ->replace('\\', '/')
                ->after('/')
                ->finish('/');

            $ignoredFolders = collect(config('ddd.autoload_ignore', []))
                ->map(fn ($path) => Str::finish($path, '/'));

            if ($pathAfterDomain->startsWith($ignoredFolders)) {
                return false;
            }
        };

        return Finder::create()
            ->files()
            ->in($paths)
            ->filter($filter);
    }

    protected static function discoverProviders(): array
    {
        $configValue = config('ddd.autoload.providers');

        if ($configValue === false) {
            return [];
        }

        $paths = static::normalizePaths(
            $configValue === true ? app()->basePath(DomainResolver::domainPath()) : $configValue
        );

        if (empty($paths)) {
            return [];
        }

        return Lody::classesFromFinder(static::finder($paths))
            ->isNotAbstract()
            ->isInstanceOf(ServiceProvider::class)
            ->toArray();
    }

    protected static function discoverCommands(): array
    {
        $configValue = config('ddd.autoload.commands');

        if ($configValue === false) {
            return [];
        }

        $paths = static::normalizePaths(
            $configValue === true ?
                app()->basePath(DomainResolver::domainPath())
                : $configValue
        );

        if (empty($paths)) {
            return [];
        }

        return Lody::classesFromFinder(static::finder($paths))
            ->isNotAbstract()
            ->isInstanceOf(Command::class)
            ->toArray();
    }

    public static function cacheProviders(): void
    {
        DomainCache::set('domain-providers', static::discoverProviders());
    }

    public static function cacheCommands(): void
    {
        DomainCache::set('domain-commands', static::discoverCommands());
    }

    protected static function appNamespace()
    {
        try {
            return Container::getInstance()
                ->make(Application::class)
                ->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
    }
}
