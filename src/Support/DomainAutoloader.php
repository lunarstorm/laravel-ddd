<?php

namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Lorisleiva\Lody\Lody;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;
use Symfony\Component\Finder\Finder;
use Throwable;

class DomainAutoloader
{
    protected string $cacheDirectory;

    protected mixed $config;

    public function __construct()
    {
        $this->config = config('ddd');
        $this->cacheDirectory = $this->configValue('cache_directory') ?? 'bootstrap/cache/ddd';
    }

    protected function configValue($path)
    {
        return data_get($this->config, $path);
    }

    public function autoload(): void
    {
        if ($value = $this->configValue('autoload.service_providers')) {
            $this->registerDomainServiceProviders($value);
        }

        if ($value = $this->configValue('autoload.commands')) {
            $this->registerDomainCommands($value);
        }

        if ($value = $this->configValue('autoload.policies')) {
            $this->registerPolicies($value);
        }

        if ($value = $this->configValue('autoload.factories')) {
            $this->registerFactories($value);
        }
    }

    public function registerDomainServiceProviders(bool|string|null $domainPath = null): void
    {
        // $domainPath = is_string($domainPath) ? $domainPath : '*/*ServiceProvider.php';

        // $serviceProviders = $this->remember('ddd-domain-service-providers', static function () use ($domainPath) {
        //     return Arr::map(
        //         glob(base_path(DomainResolver::domainPath() . '/' . $domainPath)),
        //         (static function ($serviceProvider) {

        //             return Path::filePathToNamespace(
        //                 $serviceProvider,
        //                 DomainResolver::domainPath(),
        //                 DomainResolver::domainRootNamespace()
        //             );
        //         })
        //     );
        // });

        $domainPath = app()->basePath(DomainResolver::domainPath());

        if (! is_dir($domainPath)) {
            return;
        }

        $serviceProviders = $this->remember('ddd-domain-service-providers', static function () use ($domainPath) {
            $finder = Finder::create()->files()->in($domainPath);

            return Lody::classesFromFinder($finder)
                ->isNotAbstract()
                ->isInstanceOf(ServiceProvider::class)
                ->toArray();
        });

        $app = app();

        foreach ($serviceProviders as $serviceProvider) {
            $app->register($serviceProvider);
        }
    }

    public function registerDomainCommands(bool|string|null $domainPath = null): void
    {
        // $domainPath = is_string($domainPath) ? $domainPath : '*/Commands/*.php';

        $domainPath = app()->basePath(DomainResolver::domainPath());

        if (! is_dir($domainPath)) {
            return;
        }

        $commands = $this->remember('ddd-domain-commands', static function () use ($domainPath) {
            $finder = Finder::create()->files()->in($domainPath);

            return Lody::classesFromFinder($finder)
                ->isNotAbstract()
                ->isInstanceOf(Command::class)
                ->toArray();
        });

        foreach ($commands as $class) {
            $this->registerCommand($class);
        }
    }

    public function registerCommand($class)
    {
        ConsoleApplication::starting(function ($artisan) use ($class) {
            $artisan->resolve($class);
        });
    }

    public function registerPolicies(bool|string|null $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : 'Policies\\{model}Policy';

        Gate::guessPolicyNamesUsing(static function (string $class): array|string|null {
            if ($model = DomainObject::fromClass($class, 'model')) {
                return (new Domain($model->domain))
                    ->object('policy', "{$model->name}Policy")
                    ->fqn;
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

    public function registerFactories(bool|string|null $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : 'Database\\Factories\\{model}Factory';

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if (DomainResolver::isDomainClass($modelName)) {
                return DomainFactory::factoryForModel($modelName);
            }

            $appNamespace = static::appNamespace();

            $modelName = Str::startsWith($modelName, $appNamespace.'Models\\')
                ? Str::after($modelName, $appNamespace.'Models\\')
                : Str::after($modelName, $appNamespace);

            return 'Database\\Factories\\'.$modelName.'Factory';
        });
    }

    protected function remember($fileName, $callback)
    {
        // The cache is not available during booting, so we need to roll our own file based cache
        $cacheFilePath = base_path($this->cacheDirectory.'/'.$fileName.'.php');

        $data = file_exists($cacheFilePath) ? include $cacheFilePath : null;

        if (is_null($data)) {
            $data = $callback();

            file_put_contents(
                $cacheFilePath,
                '<?php '.PHP_EOL.'return '.var_export($data, true).';'
            );
        }

        return $data;
    }

    public static function clearCache()
    {
        $files = glob(base_path(config('ddd.cache_directory').'/ddd-*.php'));

        File::delete($files);
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
