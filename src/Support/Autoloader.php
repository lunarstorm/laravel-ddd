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
use Illuminate\Support\Traits\Conditionable;
use Lorisleiva\Lody\Lody;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class Autoloader
{
    use Conditionable;

    protected string $appNamespace;

    protected array $registeredCommands = [];

    protected array $registeredProviders = [];

    protected bool $isBooted = false;

    public function __construct()
    {
        $this->appNamespace = $this->resolveAppNamespace();
    }

    public function boot()
    {
        if (! config()->has('ddd.autoload')) {
            return $this;
        }

        // if ($this->isBooted) {
        //     return $this;
        // }

        $this
            ->when(config('ddd.autoload.providers') === true, fn () => $this->handleProviders())
            ->when(app()->runningInConsole() && config('ddd.autoload.commands') === true, fn () => $this->handleCommands())
            ->when(config('ddd.autoload.policies') === true, fn () => $this->handlePolicies())
            ->when(config('ddd.autoload.factories') === true, fn () => $this->handleFactories());

        if (app()->runningInConsole()) {
            ConsoleApplication::starting(function ($artisan) {
                foreach ($this->registeredCommands as $command) {
                    $artisan->resolve($command);
                }
            });
        }

        $this->isBooted = true;

        return $this;
    }

    public function run() {}

    protected function normalizePaths($path): array
    {
        return collect($path)
            ->filter(fn ($path) => is_dir($path))
            ->toArray();
    }

    public function getAllLayerPaths(): array
    {
        return collect([
            DomainResolver::domainPath(),
            DomainResolver::applicationLayerPath(),
            ...array_values(config('ddd.layers', [])),
        ])->map(fn ($path) => app()->basePath($path))->toArray();
    }

    protected function getCustomLayerPaths(): array
    {
        return collect([
            ...array_values(config('ddd.layers', [])),
        ])->map(fn ($path) => app()->basePath($path))->toArray();
    }

    protected function handleProviders()
    {
        $providers = DomainCache::has('domain-providers')
            ? DomainCache::get('domain-providers')
            : $this->discoverProviders();

        $this->registeredProviders = [];

        foreach ($providers as $provider) {
            $this->registeredProviders[$provider] = $provider;
            app()->register($provider);
        }

        return $this;
    }

    protected function handleCommands()
    {
        $commands = DomainCache::has('domain-commands')
            ? DomainCache::get('domain-commands')
            : $this->discoverCommands();

        $this->registeredCommands = [];

        foreach ($commands as $command) {
            $this->registeredCommands[$command] = $command;
            $this->registerCommand($command);
        }

        return $this;
    }

    public function getRegisteredCommands(): array
    {
        return $this->registeredCommands;
    }

    public function getRegisteredProviders(): array
    {
        return $this->registeredProviders;
    }

    protected function registerCommand($class)
    {
        // ConsoleApplication::starting(function ($artisan) use ($class) {
        //     dump('resolving command', $class, $this->registeredCommands);
        //     $artisan->resolve($class);
        // });
    }

    protected function handlePolicies()
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

        return $this;
    }

    protected function handleFactories()
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if ($factoryName = DomainFactory::resolveFactoryName($modelName)) {
                return $factoryName;
            }

            $modelName = Str::startsWith($modelName, $this->appNamespace.'Models\\')
                ? Str::after($modelName, $this->appNamespace.'Models\\')
                : Str::after($modelName, $this->appNamespace);

            return 'Database\\Factories\\'.$modelName.'Factory';
        });

        return $this;
    }

    protected function finder($paths)
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

    protected function discoverProviders(): array
    {
        $configValue = config('ddd.autoload.providers');

        if ($configValue === false) {
            return [];
        }

        $paths = $this->normalizePaths(
            $configValue === true
                ? $this->getAllLayerPaths()
                : $configValue
        );

        if (empty($paths)) {
            return [];
        }

        return Lody::classesFromFinder($this->finder($paths))
            ->isNotAbstract()
            ->isInstanceOf(ServiceProvider::class)
            ->toArray();
    }

    protected function discoverCommands(): array
    {
        $configValue = config('ddd.autoload.commands');

        if ($configValue === false) {
            return [];
        }

        $paths = $this->normalizePaths(
            $configValue === true
                ? $this->getAllLayerPaths()
                : $configValue
        );

        if (empty($paths)) {
            return [];
        }

        return Lody::classesFromFinder($this->finder($paths))
            ->isNotAbstract()
            ->isInstanceOf(Command::class)
            ->toArray();
    }

    public function cacheProviders()
    {
        DomainCache::set('domain-providers', $this->discoverProviders());

        return $this;
    }

    public function cacheCommands()
    {
        DomainCache::set('domain-commands', $this->discoverCommands());

        return $this;
    }

    protected function resolveAppNamespace()
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
