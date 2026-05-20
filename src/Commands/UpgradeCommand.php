<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;

class UpgradeCommand extends Command
{
    protected $name = 'ddd:upgrade';

    protected $description = 'Upgrade the package configuration and application code for compatibility with the latest version.';

    const OLD_NAMESPACE = 'Lunarstorm\\LaravelDDD';

    const NEW_NAMESPACE = 'Tey\\LaravelDDD';

    const OLD_PACKAGE = 'lunarstorm/laravel-ddd';

    const NEW_PACKAGE = 'tey/laravel-ddd';

    public function handle()
    {
        if (! file_exists(config_path('ddd.php'))) {
            $this->components->warn('Config file was not published. Nothing to upgrade!');

            return self::SUCCESS;
        }

        if ($this->handleV3Upgrade()) {
            return self::SUCCESS;
        }

        $this->handleConfigUpgrade();

        return self::SUCCESS;
    }

    protected function isNewPackageInstalled(): bool
    {
        return InstalledVersions::isInstalled(static::NEW_PACKAGE);
    }

    protected function makeProcess(array $command, string $cwd): Process
    {
        return new Process($command, $cwd);
    }

    protected function handleV3Upgrade(): bool
    {
        if ($this->isNewPackageInstalled()) {
            return false;
        }

        $this->components->info('An upgrade to v3 ('.static::NEW_PACKAGE.') is available.');
        $this->newLine();

        $this->line(sprintf(
            '  This will install <fg=green>%s:^3.0</>, remove <fg=yellow>%s</>, and migrate your application code.',
            static::NEW_PACKAGE,
            static::OLD_PACKAGE,
        ));

        $this->newLine();

        if (! confirm('Proceed with upgrade to v3?', default: true)) {
            $this->components->warn('Upgrade skipped. See UPGRADING.md for manual steps.');

            return false;
        }

        if (! $this->runComposerPackageSwap()) {
            // Composer swap failed — don't fall through to the config upgrade.
            return true;
        }

        $this->migrateConfigNamespaces();
        $this->migrateAppNamespaces();

        $this->newLine();
        $this->components->info('Running ddd:upgrade from the new package to complete config migration...');
        $this->newLine();

        $process = $this->makeProcess([PHP_BINARY, 'artisan', 'ddd:upgrade'], base_path());
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->components->warn('ddd:upgrade from the new package exited with errors. Please review and re-run manually.');
        }

        $this->newLine();
        $this->components->info('Upgrade to v3 complete!');

        return true;
    }

    protected function runComposerPackageSwap(): bool
    {
        $this->components->info('Running composer package swap...');

        $requireProcess = $this->makeProcess(
            ['composer', 'require', static::NEW_PACKAGE.':^3.0', '--no-interaction'],
            base_path(),
        );

        $requireProcess->setTimeout(300);
        $requireProcess->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $requireProcess->isSuccessful()) {
            $this->components->error('Failed to install '.static::NEW_PACKAGE.'. Aborting upgrade.');

            return false;
        }

        $removeProcess = $this->makeProcess(
            ['composer', 'remove', static::OLD_PACKAGE, '--no-interaction'],
            base_path(),
        );

        $removeProcess->setTimeout(300);
        $removeProcess->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $removeProcess->isSuccessful()) {
            $this->components->warn(static::OLD_PACKAGE.' could not be removed automatically. You may remove it manually.');
        }

        return true;
    }

    protected function migrateConfigNamespaces(): void
    {
        $configPath = config_path('ddd.php');
        $contents = file_get_contents($configPath);

        if (! str_contains($contents, static::OLD_NAMESPACE)) {
            return;
        }

        $updated = str_replace(static::OLD_NAMESPACE, static::NEW_NAMESPACE, $contents);
        file_put_contents($configPath, $updated);

        $this->components->twoColumnDetail('Updated namespace references in config/ddd.php', '<fg=green;options=bold>DONE</>');
    }

    protected function migrateAppNamespaces(): void
    {
        $searchPaths = array_filter([
            app_path(),
            base_path('src'),
        ], fn ($path) => is_dir($path));

        if (empty($searchPaths)) {
            return;
        }

        $finder = Finder::create()
            ->in($searchPaths)
            ->name('*.php')
            ->contains(static::OLD_NAMESPACE)
            ->files();

        $files = iterator_to_array($finder);

        if (empty($files)) {
            $this->components->twoColumnDetail('No application files require namespace updates', '<fg=green;options=bold>NONE</>');

            return;
        }

        $this->components->warn(sprintf(
            'Found %d file(s) referencing the old namespace <fg=yellow>%s</>:',
            count($files),
            static::OLD_NAMESPACE,
        ));

        foreach ($files as $file) {
            $this->line('  <fg=gray>'.$file->getRelativePathname().'</>');
        }

        $this->newLine();

        if (! confirm(sprintf(
            'Replace all occurrences of [%s] with [%s]?',
            static::OLD_NAMESPACE,
            static::NEW_NAMESPACE,
        ), default: true)) {
            $this->components->warn('Skipped. You will need to update these files manually.');

            return;
        }

        foreach ($files as $file) {
            $updated = str_replace(
                static::OLD_NAMESPACE,
                static::NEW_NAMESPACE,
                file_get_contents($file->getRealPath()),
            );

            file_put_contents($file->getRealPath(), $updated);

            $this->components->twoColumnDetail($file->getRelativePathname(), '<fg=green;options=bold>UPDATED</>');
        }
    }

    protected function handleConfigUpgrade(): void
    {
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
