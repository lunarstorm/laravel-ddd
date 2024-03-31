<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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

        $replacements = [
            'domain_path' => 'paths.domain',
            'domain_namespace' => 'domain_namespace',
            'namespaces.model' => 'namespaces.models',
            'namespaces.data_transfer_object' => 'namespaces.data_transfer_objects',
            'namespaces.view_model' => 'namespaces.view_models',
            'namespaces.value_object' => 'namespaces.value_objects',
            'namespaces.action' => 'namespaces.actions',
            'base_model' => 'base_model',
            'base_dto' => 'base_dto',
            'base_view_model' => 'base_view_model',
            'base_action' => 'base_action',
        ];

        $oldConfig = require config_path('ddd.php');
        $oldConfig = Arr::dot($oldConfig);

        // Grab a flesh copy of the new config
        $newConfigContent = file_get_contents(__DIR__.'/../../config/ddd.php.stub');

        foreach ($replacements as $dotPath => $legacyKey) {
            $value = match (true) {
                array_key_exists($dotPath, $oldConfig) => $oldConfig[$dotPath],
                array_key_exists($legacyKey, $oldConfig) => $oldConfig[$legacyKey],
                default => config("ddd.{$dotPath}"),
            };

            $newConfigContent = str_replace(
                '{{'.$dotPath.'}}',
                var_export($value, true),
                $newConfigContent
            );
        }

        // Write the new config to the config file
        file_put_contents(config_path('ddd.php'), $newConfigContent);

        $this->components->info('Configuration upgraded successfully.');
    }
}
