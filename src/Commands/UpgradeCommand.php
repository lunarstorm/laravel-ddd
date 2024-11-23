<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class UpgradeCommand extends Command
{
    protected $name = 'ddd:upgrade';

    protected $description = 'Upgrade published config files for compatibility with 1.x.';

    public function handle()
    {
        if (! file_exists(config_path('ddd.php'))) {
            $this->components->warn('Config file was not published. Nothing to upgrade!');

            return;
        }

        $legacyMapping = [
            'domain_path' => 'paths.domain',
            'domain_namespace' => 'domain_namespace',
            'application' => null,
            'layers' => null,
            'namespaces' => [
                'model' => 'namespaces.models',
                'data_transfer_object' => 'namespaces.data_transfer_objects',
                'view_model' => 'namespaces.view_models',
                'value_object' => 'namespaces.value_objects',
                'action' => 'namespaces.actions',
            ],
            'base_model' => 'base_model',
            'base_dto' => 'base_dto',
            'base_view_model' => 'base_view_model',
            'base_action' => 'base_action',
            'autoload' => null,
            'autoload_ignore' => null,
            'cache_directory' => null,
        ];

        $factoryConfig = require __DIR__.'/../../config/ddd.php';
        $oldConfig = require config_path('ddd.php');
        $oldConfig = Arr::dot($oldConfig);

        $replacements = [];

        $map = Arr::dot($legacyMapping);

        foreach ($map as $dotPath => $legacyKey) {
            $value = match (true) {
                array_key_exists($dotPath, $oldConfig) => $oldConfig[$dotPath],
                array_key_exists($legacyKey, $oldConfig) => $oldConfig[$legacyKey],
                default => config("ddd.{$dotPath}"),
            };

            $replacements[$dotPath] = $value ?? data_get($factoryConfig, $dotPath);
        }

        $replacements = Arr::undot($replacements);

        $freshConfig = $factoryConfig;

        // Grab a fresh copy of the new config
        $newConfigContent = file_get_contents(__DIR__.'/../../config/ddd.php.stub');

        foreach ($freshConfig as $key => $value) {
            $resolved = null;

            if (is_array($value)) {
                $resolved = [
                    ...$value,
                    ...data_get($replacements, $key, []),
                ];

                if (array_is_list($resolved)) {
                    $resolved = array_unique($resolved);
                }
            } else {
                $resolved = data_get($replacements, $key, $value);
            }

            $freshConfig[$key] = $resolved;

            $newConfigContent = str_replace(
                '{{'.$key.'}}',
                var_export($resolved, true),
                $newConfigContent
            );
        }

        // Write the new config to the config file
        file_put_contents(config_path('ddd.php'), $newConfigContent);

        $this->components->info('Configuration upgraded successfully.');
    }
}
