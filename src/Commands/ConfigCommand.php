<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\ComposerManager;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\Support\Layer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\table;

class ConfigCommand extends Command
{
    public $name = 'ddd:config';

    protected $description = 'Configure or modify ddd-related assets and settings';

    protected ComposerManager $composer;

    protected function getArguments()
    {
        return [
            new InputArgument('action', InputArgument::OPTIONAL, 'The action to perform.'),
        ];
    }

    protected function getOptions()
    {
        return [
            new InputOption('layer', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Define a new layer and register it in composer.json'),
        ];
    }

    public function handle(): int
    {
        $this->composer = DDD::composer()->usingOutput($this->output);

        $action = str($this->argument('action'))->trim()->lower()->toString();

        if (! $action && $this->option('layer')) {
            $action = 'layers';
        }

        return match ($action) {
            'wizard' => $this->wizard(),
            'detect' => $this->detect(),
            'composer' => $this->syncComposer(),
            'layers' => $this->layers(),
            default => $this->home(),
        };
    }

    protected function home(): int
    {
        $action = select('Laravel-DDD Config Utility', [
            'wizard' => 'Run the configuration wizard',
            'detect' => 'Detect domain namespace from composer.json',
            'composer' => 'Sync composer.json from ddd.php',
            'exit' => 'Exit',
        ], scroll: 10);

        return match ($action) {
            'wizard' => $this->wizard(),
            'detect' => $this->detect(),
            'composer' => $this->syncComposer(),
            'exit' => $this->exit(),
            default => $this->exit(),
        };
    }

    protected function layers()
    {
        $layers = $this->option('layer');

        if ($layers = $this->option('layer')) {
            foreach ($layers as $layer) {
                $parts = explode(':', $layer);

                $this->composer->registerPsr4Autoload(
                    namespace: data_get($parts, 0),
                    path: data_get($parts, 1)
                );
            }

            $this->composer->saveAndReload();
        }

        $this->info('Configuration updated.');

        return self::SUCCESS;
    }

    protected function wizard(): int
    {
        $namespaces = collect($this->composer->getPsr4Namespaces());

        $layers = $namespaces->map(fn ($path, $namespace) => new Layer($namespace, $path));
        $laravelAppLayer = $layers->first(fn (Layer $layer) => str($layer->namespace)->exactly('App'));
        $possibleDomainLayers = $layers->filter(fn (Layer $layer) => str($layer->namespace)->startsWith('Domain'));
        $possibleApplicationLayers = $layers->filter(fn (Layer $layer) => str($layer->namespace)->startsWith('App'));

        $domainLayer = $possibleDomainLayers->first();
        $applicationLayer = $possibleApplicationLayers->first();

        $detected = collect([
            'domain_namespace' => $domainLayer?->namespace,
            'domain_path' => $domainLayer?->path,
            'application' => [
                'namespace' => $applicationLayer?->namespace,
                'path' => $applicationLayer?->path,
            ],
        ]);

        $config = $detected->merge(Config::get('ddd'));

        // dd($config);

        info('Detected DDD configuration:');

        table(
            headers: ['Key', 'Value'],
            rows: $detected->dot()->map(fn ($value, $key) => [$key, $value])->all()
        );

        $choices = [
            'domain_path' => [
                'src/Domain' => 'src/Domain',
                'src/Domains' => 'src/Domains',
                ...[
                    $config->get('domain_path') => $config->get('domain_path'),
                ],
                ...$possibleDomainLayers->mapWithKeys(
                    fn (Layer $layer) => [$layer->path => $layer->path]
                ),
            ],
            'domain_namespace' => [
                'Domain' => 'Domain',
                'Domains' => 'Domains',
                ...[
                    $config->get('domain_namespace') => $config->get('domain_namespace'),
                ],
                ...$possibleDomainLayers->mapWithKeys(
                    fn (Layer $layer) => [$layer->namespace => $layer->namespace]
                ),
            ],
            'application_path' => [
                'app/Modules' => 'app/Modules',
                'src/Modules' => 'src/Modules',
                'Modules' => 'Modules',
                'src/Application' => 'src/Application',
                'Application' => 'Application',
                ...[
                    data_get($config, 'application.path') => data_get($config, 'application.path'),
                ],
                ...$possibleApplicationLayers->mapWithKeys(
                    fn (Layer $layer) => [$layer->path => $layer->path]
                ),
            ],
            'application_namespace' => [
                'App\Modules' => 'App\Modules',
                'Application' => 'Application',
                'Modules' => 'Modules',
                ...[
                    data_get($config, 'application.namespace') => data_get($config, 'application.namespace'),
                ],
                ...$possibleApplicationLayers->mapWithKeys(
                    fn (Layer $layer) => [$layer->namespace => $layer->namespace]
                ),
            ],
            'layers' => [
                'src/Infrastructure' => 'src/Infrastructure',
                'src/Integrations' => 'src/Integrations',
                'src/Support' => 'src/Support',
            ],
        ];

        // dd($choices['application_namespace']);

        $form = form()
            ->add(
                function ($responses) use ($choices, $detected, $config) {
                    return suggest(
                        label: 'Domain Path',
                        options: $choices['domain_path'],
                        default: $detected->get('domain_path') ?: $config->get('domain_path'),
                        hint: 'The path to the domain layer relative to the base path.',
                        required: true,
                    );
                },
                name: 'domain_path'
            )
            ->add(
                function ($responses) use ($choices, $config) {
                    return suggest(
                        label: 'Domain Namespace',
                        options: $choices['domain_namespace'],
                        default: class_basename($responses['domain_path']) ?: $config->get('domain_namespace'),
                        required: true,
                        hint: 'The root domain namespace.',
                    );
                },
                name: 'domain_namespace'
            )
            ->add(
                function ($responses) use ($choices) {
                    return suggest(
                        label: 'Path to Application Layer',
                        options: $choices['application_path'],
                        hint: "For objects that don't belong in the domain layer (controllers, form requests, etc.)",
                        placeholder: 'Leave blank to skip and configure later',
                        scroll: 10,
                    );
                },
                name: 'application_path'
            )
            ->add(
                function ($responses) use ($choices, $laravelAppLayer) {
                    $applicationPath = $responses['application_path'];
                    $laravelAppPath = $laravelAppLayer->path;

                    $namespace = match (true) {
                        str($applicationPath)->exactly($laravelAppPath) => $laravelAppLayer->namespace,
                        str($applicationPath)->startsWith("{$laravelAppPath}/") => str($applicationPath)->studly()->toString(),
                        default => str($applicationPath)->classBasename()->studly()->toString(),
                    };

                    return suggest(
                        label: 'Application Layer Namespace',
                        options: $choices['application_namespace'],
                        default: $namespace,
                        hint: 'The root application namespace.',
                    );
                },
                name: 'application_namespace'
            )
            ->add(
                function ($responses) use ($choices) {
                    return multiselect(
                        label: 'Additional Layers (Optional)',
                        options: $choices['layers'],
                        hint: 'Layers can be customized in the ddd.php config file at any time.',
                    );
                },
                name: 'layers'
            );

        $responses = $form->submit();

        // dd($responses);

        return self::SUCCESS;
    }

    protected function detect(): int
    {
        $search = ['Domain', 'Domains'];

        $detected = [];

        foreach ($search as $namespace) {
            if ($path = $this->composer->getAutoloadPath($namespace)) {
                $detected['domain_namespace'] = $namespace;
                $detected['domain_path'] = $path;
                break;
            }
        }

        $this->info('Detected configuration:');

        table(
            headers: ['Config', 'Value'],
            rows: collect($detected)
                ->map(fn ($value, $key) => [$key, $value])
                ->all()
        );

        return self::SUCCESS;
    }

    protected function applyConfig(Collection $config)
    {
        // $this->composer->update([
        //     ['domain_namespace', $config['domain_namespace']],
        //     ['domain_path', $config['domain_path']],
        //     ['application.namespace', $config['application']['namespace']],
        //     ['application.path', $config['application']['path']],
        // ]);

        return self::SUCCESS;
    }

    protected function syncComposer(): int
    {
        $namespaces = [
            config('ddd.domain_namespace', 'Domain') => config('ddd.domain_path', 'src/Domain'),
            config('ddd.application.namespace', 'App\\Modules') => config('ddd.application.path', 'app/Modules'),
            ...collect(config('ddd.layers', []))
                ->all(),
        ];

        $this->info('Syncing composer.json from ddd.php...');

        $results = [];

        $added = 0;

        foreach ($namespaces as $namespace => $path) {
            if ($this->composer->hasPsr4Autoload($namespace)) {
                $results[] = [$namespace, $path, 'Already Registered'];

                continue;
            }

            $rootNamespace = Str::before($namespace, '\\');

            if ($this->composer->hasPsr4Autoload($rootNamespace)) {
                $results[] = [$namespace, $path, 'Skipped'];

                continue;
            }

            $this->composer->registerPsr4Autoload($rootNamespace, $path);

            $results[] = [$namespace, $path, 'Added'];

            $added++;
        }

        if ($added > 0) {
            $this->composer->saveAndReload();
        }

        table(
            headers: ['Namespace', 'Path', 'Status'],
            rows: $results
        );

        return self::SUCCESS;
    }

    protected function exit(): int
    {
        $this->info('Goodbye!');

        return self::SUCCESS;
    }
}
