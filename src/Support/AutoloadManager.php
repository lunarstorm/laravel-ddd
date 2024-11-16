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

class AutoloadManager
{
    use Conditionable;

    protected string $appNamespace;

    protected array $registeredCommands = [];

    protected array $registeredProviders = [];

    protected array $resolvedPolicies = [];

    protected array $resolvedFactories = [];

    protected bool $booted = false;

    protected bool $consoleBooted = false;

    public function __construct()
    {
        $this->appNamespace = $this->resolveAppNamespace();
    }

    public function boot()
    {
        if (! config()->has('ddd.autoload')) {
            return $this;
        }

        $this
            ->flush()
            ->when(config('ddd.autoload.providers') === true, fn () => $this->handleProviders())
            ->when(app()->runningInConsole() && config('ddd.autoload.commands') === true, fn () => $this->handleCommands())
            ->when(config('ddd.autoload.policies') === true, fn () => $this->handlePolicies())
            ->when(config('ddd.autoload.factories') === true, fn () => $this->handleFactories())
            ->run();

        $this->booted = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function isConsoleBooted(): bool
    {
        return $this->consoleBooted;
    }

    protected function flush()
    {
        foreach ($this->registeredProviders as $provider) {
            app()->forgetInstance($provider);
        }

        $this->registeredProviders = [];

        $this->registeredCommands = [];

        $this->resolvedPolicies = [];

        $this->resolvedFactories = [];

        return $this;
    }

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

        foreach ($this->registeredProviders as $provider) {
            app()->forgetInstance($provider);
        }

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
        }

        return $this;
    }

    protected function run()
    {
        foreach ($this->registeredProviders as $provider) {
            app()->register($provider);
        }

        if (app()->runningInConsole() && ! $this->isConsoleBooted()) {
            ConsoleApplication::starting(function (ConsoleApplication $artisan) {
                foreach ($this->registeredCommands as $command) {
                    $artisan->resolve($command);
                }
            });

            $this->consoleBooted = true;
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

    public function getResolvedPolicies(): array
    {
        return $this->resolvedPolicies;
    }

    public function getResolvedFactories(): array
    {
        return $this->resolvedFactories;
    }

    protected function handlePolicies()
    {
        Gate::guessPolicyNamesUsing(function (string $class): array|string {
            if (array_key_exists($class, $this->resolvedPolicies)) {
                return $this->resolvedPolicies[$class];
            }

            if ($model = DomainObject::fromClass($class, 'model')) {
                $resolved = (new Domain($model->domain))
                    ->object('policy', "{$model->name}Policy")
                    ->fullyQualifiedName;

                $this->resolvedPolicies[$class] = $resolved;

                return $resolved;
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
            if (array_key_exists($modelName, $this->resolvedFactories)) {
                return $this->resolvedFactories[$modelName];
            }

            if ($factoryName = DomainFactory::resolveFactoryName($modelName)) {
                $this->resolvedFactories[$modelName] = $factoryName;

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

    public function cacheCommands()
    {
        DomainCache::set('domain-commands', $this->discoverCommands());

        return $this;
    }

    public function cacheProviders()
    {
        DomainCache::set('domain-providers', $this->discoverProviders());

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
