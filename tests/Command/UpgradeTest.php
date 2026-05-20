<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\Commands\UpgradeCommand;
use Symfony\Component\Process\Process;

it('can upgrade 0.x config to 1.x', function (string $pathToOldConfig, array $expectedValues) {
    $configFilePath = config_path('ddd.php');

    File::copy($pathToOldConfig, $configFilePath);

    expect(file_exists($configFilePath))->toBeTrue();

    // Decline the v3 upgrade so the command falls through to config upgrade.
    // In the test environment tey/laravel-ddd is not installed, so the prompt always shows.
    $this->artisan('ddd:upgrade')
        ->expectsConfirmation('Proceed with upgrade to v3?', 'no')
        ->expectsOutputToContain('Configuration upgraded successfully.')
        ->execute();

    Artisan::call('config:clear');

    $expectedValues = Arr::dot($expectedValues);

    $configAsArray = require config_path('ddd.php');

    foreach ($expectedValues as $key => $value) {
        expect(data_get($configAsArray, $key))
            ->toEqual($value, "Config {$key} does not match expected value.");
    }

    // Delete the config file after the test
    unlink($configFilePath);
})->with('configUpgrades');

it('skips upgrade if config file was not published', function () {
    $path = config_path('ddd.php');

    if (file_exists($path)) {
        unlink($path);
    }

    expect(file_exists($path))->toBeFalse();

    $this->artisan('ddd:upgrade')
        ->expectsOutputToContain('Config file was not published. Nothing to upgrade!')
        ->execute();

    expect(file_exists($path))->toBeFalse();
});

describe('v3 upgrade', function () {
    /**
     * Returns a subclass of UpgradeCommand with controllable Process calls and
     * isNewPackageInstalled() for isolated testing.
     */
    function makeUpgradeCommand(bool $newPackageInstalled, bool $composerSwapSucceeds = true): UpgradeCommand
    {
        return new class($newPackageInstalled, $composerSwapSucceeds) extends UpgradeCommand {
            public function __construct(
                private readonly bool $newPackageInstalled,
                private readonly bool $composerSwapSucceeds,
            ) {
                parent::__construct();
            }

            protected function isNewPackageInstalled(): bool
            {
                return $this->newPackageInstalled;
            }

            protected function makeProcess(array $command, string $cwd): Process
            {
                $succeeds = $this->composerSwapSucceeds;

                return new class($succeeds) extends Process {
                    public function __construct(private readonly bool $succeeds)
                    {
                        // Skip parent constructor — no real command needed in tests.
                    }

                    public function setTimeout(?float $timeout): static
                    {
                        return $this;
                    }

                    public function run(?callable $callback = null, array $env = []): int
                    {
                        return $this->succeeds ? 0 : 1;
                    }

                    public function isSuccessful(): bool
                    {
                        return $this->succeeds;
                    }
                };
            }
        };
    }

    beforeEach(function () {
        $configFilePath = config_path('ddd.php');
        if (! file_exists($configFilePath)) {
            File::copy(__DIR__.'/../../config/ddd.php', $configFilePath);
        }
    });

    afterEach(function () {
        $path = config_path('ddd.php');
        if (file_exists($path)) {
            unlink($path);
        }
    });

    it('skips v3 upgrade prompt when tey/laravel-ddd is already installed', function () {
        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(newPackageInstalled: true));

        $this->artisan('ddd:upgrade')
            ->doesntExpectOutputToContain('upgrade to v3')
            ->expectsOutputToContain('Configuration upgraded successfully.')
            ->execute();
    });

    it('shows v3 upgrade prompt when tey/laravel-ddd is not yet installed', function () {
        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(newPackageInstalled: false));

        $this->artisan('ddd:upgrade')
            ->expectsOutputToContain('upgrade to v3')
            ->expectsConfirmation('Proceed with upgrade to v3?', 'no')
            ->execute();
    });

    it('skips v3 upgrade when user declines', function () {
        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(newPackageInstalled: false));

        $this->artisan('ddd:upgrade')
            ->expectsConfirmation('Proceed with upgrade to v3?', 'no')
            ->expectsOutputToContain('Upgrade skipped')
            ->execute();
    });

    it('falls through to config upgrade when v3 upgrade is declined', function () {
        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(newPackageInstalled: false));

        $this->artisan('ddd:upgrade')
            ->expectsConfirmation('Proceed with upgrade to v3?', 'no')
            ->expectsOutputToContain('Configuration upgraded successfully.')
            ->execute();
    });

    it('rewrites old namespace references in config/ddd.php after v3 upgrade', function () {
        $configPath = config_path('ddd.php');

        $modified = str_replace(
            "'base_model' => null",
            "'base_model' => 'Lunarstorm\\LaravelDDD\\Models\\DomainModel'",
            file_get_contents($configPath),
        );
        file_put_contents($configPath, $modified);

        expect(file_get_contents($configPath))->toContain('Lunarstorm\LaravelDDD');

        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(newPackageInstalled: false));

        $this->artisan('ddd:upgrade')
            ->expectsConfirmation('Proceed with upgrade to v3?', 'yes')
            ->expectsOutputToContain('No application files require namespace updates')
            ->execute();

        expect(file_get_contents($configPath))
            ->not->toContain('Lunarstorm\LaravelDDD')
            ->toContain('Tey\LaravelDDD');
    });

    it('replaces old namespace in app php files when confirmed', function () {
        $tmpFile = app_path('SomeService.php');
        File::ensureDirectoryExists(app_path());
        file_put_contents($tmpFile, "<?php\nuse Lunarstorm\\LaravelDDD\\Facades\\DDD;\n");

        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(newPackageInstalled: false));

        $this->artisan('ddd:upgrade')
            ->expectsConfirmation('Proceed with upgrade to v3?', 'yes')
            ->expectsConfirmation(
                'Replace all occurrences of [Lunarstorm\LaravelDDD] with [Tey\LaravelDDD]?',
                'yes',
            )
            ->execute();

        expect(file_get_contents($tmpFile))
            ->not->toContain('Lunarstorm\\LaravelDDD')
            ->toContain('Tey\\LaravelDDD');

        unlink($tmpFile);
    });

    it('skips replacing app files when not confirmed', function () {
        $tmpFile = app_path('SomeService.php');
        File::ensureDirectoryExists(app_path());
        file_put_contents($tmpFile, "<?php\nuse Lunarstorm\\LaravelDDD\\Facades\\DDD;\n");

        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(newPackageInstalled: false));

        $this->artisan('ddd:upgrade')
            ->expectsConfirmation('Proceed with upgrade to v3?', 'yes')
            ->expectsConfirmation(
                'Replace all occurrences of [Lunarstorm\LaravelDDD] with [Tey\LaravelDDD]?',
                'no',
            )
            ->expectsOutputToContain('Skipped. You will need to update these files manually.')
            ->execute();

        expect(file_get_contents($tmpFile))->toContain('Lunarstorm\\LaravelDDD');

        unlink($tmpFile);
    });

    it('aborts and does not migrate when composer require fails', function () {
        $configPath = config_path('ddd.php');

        $modified = str_replace(
            "'base_model' => null",
            "'base_model' => 'Lunarstorm\\LaravelDDD\\Models\\DomainModel'",
            file_get_contents($configPath),
        );
        file_put_contents($configPath, $modified);

        $this->app->instance(UpgradeCommand::class, makeUpgradeCommand(
            newPackageInstalled: false,
            composerSwapSucceeds: false,
        ));

        $this->artisan('ddd:upgrade')
            ->expectsConfirmation('Proceed with upgrade to v3?', 'yes')
            ->expectsOutputToContain('Failed to install tey/laravel-ddd')
            ->execute();

        // Namespace should NOT have been updated since composer failed
        expect(file_get_contents($configPath))->toContain('Lunarstorm\LaravelDDD');
    });
});
