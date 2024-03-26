<?php
namespace Lunarstorm\LaravelDDD\Support;

use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;

class DomainAutoloader
{
    protected string $cacheDirectory;

    protected mixed $config;

    public function __construct()
    {
        $this->config = config('ddd');
        $this->cacheDirectory = $this->config['cache_directory'] ?? 'bootstrap/cache/ddd';
    }

    public function autoload(): void
    {
        if(isset($this->config['autoload']['service_providers'])) {
            $this->registerDomainServiceProviders($this->config['autoload']['service_providers']);
        }
        if(isset($this->config['autoload']['commands'])) {
            $this->registerDomainCommands($this->config['autoload']['commands']);
        }
        if(isset($this->config['autoload']['policies'])) {
            $this->registerPolicies($this->config['autoload']['policies']);
        }
        if(isset($this->config['autoload']['factories'])) {
            $this->registerFactories($this->config['autoload']['factories']);
        }
    }

    protected function registerDomainServiceProviders(bool|string $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : '*/*ServiceProvider.php';

        $serviceProviders = $this->remember('ddd-domain-service-providers', static function () use ($domainPath){
            return Arr::map(
                glob(base_path(DomainResolver::domainPath().'/'.$domainPath)),
                (static function ($serviceProvider) {

                return Path::filePathToNamespace(
                    $serviceProvider,
                    DomainResolver::domainPath(),
                    DomainResolver::domainRootNamespace()
                );
            }));
        });

        $app = app();
        foreach ($serviceProviders as $serviceProvider) {
            $app->register($serviceProvider);
        }
    }

    protected function registerDomainCommands(bool|string $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : '*/Commands/*.php';
        $commands = $this->remember('ddd-domain-commands', static function () use ($domainPath){
            $commands = Arr::map(
                glob(base_path(DomainResolver::domainPath().'/'.$domainPath)),
                static function ($command) {
                    return Path::filePathToNamespace(
                        $command,
                        DomainResolver::domainPath(),
                        DomainResolver::domainRootNamespace()
                    );
            });

            // Filter out invalid commands (Abstract classes and classes not extending Illuminate\Console\Command)
            return Arr::where($commands, static function($command) {
                if (is_subclass_of($command, Command::class) &&
                    ! (new ReflectionClass($command))->isAbstract()) {
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

    protected function registerPolicies(bool|string $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : 'Policies\\{model}Policy';

        Gate::guessPolicyNamesUsing(static function (string $modelClass) use ($domainPath): ?string {

            [$domain, $model] = static::extractDomainAndModelFromModelNamespace($modelClass);

            if (is_null($domain)) {
                return null;
            }

            $policy = DomainResolver::domainRootNamespace().'\\'.$domain.'\\'.str_replace('{model}', $model, $domainPath);

            return $policy;
        });
    }

    protected function registerFactories(bool|string $domainPath = null): void
    {
        $domainPath = is_string($domainPath) ? $domainPath : 'Database\\Factories\\{model}Factory';

        Factory::guessFactoryNamesUsing( function (string $modelClass) use ($domainPath){

            [$domain, $model] = $this->extractDomainAndModelFromModelNamespace($modelClass);

            if (is_null($domain)) {
                return null;
            }

            // Look for domain model factory in \<DomainNamespace>\Database\\Factories\<model>Factory.php
            $classPath = 'Domain\\'.$domain.'\\'.str_replace('{model}', $model, $domainPath);
            if (class_exists($classPath)) {
                return $classPath;
            }

            // Look for domain factory in /database/factories/<domain>/<model>Factory.php
            $classPath = 'Database\\Factories\\'.$domain.'\\'.$model.'Factory';
            if (class_exists($classPath)) {
                return $classPath;
            }

            // Default Laravel factory location
            return 'Database\Factories\\'.class_basename($modelClass).'Factory';
        });
    }

    protected function extractDomainAndModelFromModelNamespace(string $modelName): array
    {
        // Matches <DomainNamespace>\{domain}\<ModelNamespace>\{model} and extracts domain and model
        // For example: Domain\Invoicing\Models\Invoice gives ['domain' => 'Invoicing', 'model' => 'Invoice']
        $regex = '/'.DomainResolver::domainRootNamespace().'\\\\(?<domain>.+)\\\\'.$this->config['namespaces.models'].'\\\\(?<model>.+)/';

        if (preg_match($regex, $modelName, $matches, PREG_OFFSET_CAPTURE, 0)) {
            return [
                'domain' => $matches['domain'][0],
                'model' => $matches['model'][0]
            ];
        }

        return [];
    }

    protected function remember($fileName, $callback)
    {
        // The cache is not available during booting, so we need to roll our own file based cache
        $cacheFilePath =  base_path($this->cacheDirectory.'/'.$fileName.'.php');

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
}
