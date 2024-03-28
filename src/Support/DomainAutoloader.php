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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;
use ReflectionClass;
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

    protected function registerDomainServiceProviders(bool|string|null $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : '*/*ServiceProvider.php';

        $serviceProviders = $this->remember('ddd-domain-service-providers', static function () use ($domainPath) {
            return Arr::map(
                glob(base_path(DomainResolver::domainPath() . '/' . $domainPath)),
                (static function ($serviceProvider) {

                    return Path::filePathToNamespace(
                        $serviceProvider,
                        DomainResolver::domainPath(),
                        DomainResolver::domainRootNamespace()
                    );
                })
            );
        });

        $app = app();
        foreach ($serviceProviders as $serviceProvider) {
            $app->register($serviceProvider);
        }
    }

    protected function registerDomainCommands(bool|string|null $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : '*/Commands/*.php';
        $commands = $this->remember('ddd-domain-commands', static function () use ($domainPath) {
            $commands = Arr::map(
                glob(base_path(DomainResolver::domainPath() . '/' . $domainPath)),
                static function ($command) {
                    return Path::filePathToNamespace(
                        $command,
                        DomainResolver::domainPath(),
                        DomainResolver::domainRootNamespace()
                    );
                }
            );

            // Filter out invalid commands (Abstract classes and classes not extending Illuminate\Console\Command)
            return Arr::where($commands, static function ($command) {
                if (
                    is_subclass_of($command, Command::class) &&
                    !(new ReflectionClass($command))->isAbstract()
                ) {
                    ConsoleApplication::starting(static function ($artisan) use ($command): void {
                        $artisan->resolve($command);
                    });
                }
            });
        });
        ConsoleApplication::starting(static function ($artisan) use ($commands): void {
            foreach ($commands as $command) {
                $artisan->resolve($command);
            }
        });
    }

    protected function registerPolicies(bool|string|null $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : 'Policies\\{model}Policy';

        Gate::guessPolicyNamesUsing(static function (string $class) use ($domainPath): array|string|null {
            if ($model = DomainObject::fromClass($class, 'model')) {
                return (new Domain($model->domain))
                    ->object('policy', "{$model->name}Policy")
                    ->fqn;
            }

            $classDirname = str_replace('/', '\\', dirname(str_replace('\\', '/', $class)));

            $classDirnameSegments = explode('\\', $classDirname);

            return Arr::wrap(Collection::times(count($classDirnameSegments), function ($index) use ($class, $classDirnameSegments) {
                $classDirname = implode('\\', array_slice($classDirnameSegments, 0, $index));

                return $classDirname . '\\Policies\\' . class_basename($class) . 'Policy';
            })->reverse()->values()->first(function ($class) {
                return class_exists($class);
            }) ?: [$classDirname . '\\Policies\\' . class_basename($class) . 'Policy']);
        });
    }

    protected function registerFactories(bool|string|null $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : 'Database\\Factories\\{model}Factory';

        Factory::guessFactoryNamesUsing(function (string $modelName) use ($domainPath) {
            if (DomainResolver::isDomainClass($modelName)) {
                return DomainFactory::factoryForModel($modelName);
            }

            $appNamespace = static::appNamespace();

            $modelName = Str::startsWith($modelName, $appNamespace . 'Models\\')
                ? Str::after($modelName, $appNamespace . 'Models\\')
                : Str::after($modelName, $appNamespace);

            return 'Database\\Factories\\' . $modelName . 'Factory';
        });
    }

    protected function extractDomainAndModelFromModelNamespace(string $modelName): array
    {
        // Matches <DomainNamespace>\{domain}\<ModelNamespace>\{model} and extracts domain and model
        // For example: Domain\Invoicing\Models\Invoice gives ['domain' => 'Invoicing', 'model' => 'Invoice']
        $regex = '/' . DomainResolver::domainRootNamespace() . '\\\\(?<domain>.+)\\\\' . $this->configValue('namespaces.models') . '\\\\(?<model>.+)/';

        if (preg_match($regex, $modelName, $matches, PREG_OFFSET_CAPTURE, 0)) {
            return [
                'domain' => $matches['domain'][0],
                'model' => $matches['model'][0],
            ];
        }

        return [];
    }

    protected function remember($fileName, $callback)
    {
        // The cache is not available during booting, so we need to roll our own file based cache
        $cacheFilePath = base_path($this->cacheDirectory . '/' . $fileName . '.php');

        $data = file_exists($cacheFilePath) ? include $cacheFilePath : null;

        if (is_null($data)) {
            $data = $callback();

            file_put_contents(
                $cacheFilePath,
                '<?php ' . PHP_EOL . 'return ' . var_export($data, true) . ';'
            );
        }

        return $data;
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
