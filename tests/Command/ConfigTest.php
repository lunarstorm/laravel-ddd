<?php

use Illuminate\Database\Migrations\MigrationCreator;
use Lunarstorm\LaravelDDD\Commands\ConfigCommand;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->setupTestApplication();

    // $this->app->when(MigrationCreator::class)
    //     ->needs('$customStubPath')
    //     ->give(fn() => $this->app->basePath('stubs'));

    $this->originalComposerContents = file_get_contents(base_path('composer.json'));

    // $this->artisan('clear-compiled')->assertSuccessful()->execute();
    // $this->artisan('optimize:clear')->assertSuccessful()->execute();
});

afterEach(function () {
    $this->cleanSlate();

    file_put_contents(base_path('composer.json'), $this->originalComposerContents);

    $this->artisan('optimize:clear')->assertSuccessful()->execute();
});

it('can run the config wizard', function () {
    $this->artisan('config:cache')->assertSuccessful()->execute();

    expect(config('ddd.domain_path'))->toBe('src/Domain');
    expect(config('ddd.domain_namespace'))->toBe('Domain');
    expect(config('ddd.layers'))->toBe([
        'Infrastructure' => 'src/Infrastructure',
    ]);

    $this->reloadApplication();

    $configPath = config_path('ddd.php');

    $this->artisan('ddd:config')
        ->expectsQuestion('Laravel-DDD Config Utility', 'wizard')
        ->expectsQuestion('Domain Path', 'src/CustomDomain')
        ->expectsQuestion('Domain Namespace', 'CustomDomain')
        ->expectsQuestion('Path to Application Layer', null)
        ->expectsQuestion('Custom Layers (Optional)', ['Support' => 'src/Support'])
        ->expectsOutput('Building configuration...')
        ->expectsOutput("Configuration updated: {$configPath}")
        ->assertSuccessful()
        ->execute();

    expect(file_exists($configPath))->toBeTrue();

    $this->artisan('config:cache')->assertSuccessful()->execute();

    expect(config('ddd.domain_path'))->toBe('src/CustomDomain');
    expect(config('ddd.domain_namespace'))->toBe('CustomDomain');
    expect(config('ddd.application_path'))->toBe('src/Application');
    expect(config('ddd.application_namespace'))->toBe('Application');
    expect(config('ddd.layers'))->toBe([
        'Support' => 'src/Support',
    ]);

    $this->artisan('config:clear')->assertSuccessful()->execute();

    unlink($configPath);
})->skip(fn () => ! ConfigCommand::hasRequiredVersionOfLaravelPrompts());

it('requires supported version of Laravel Prompts to run the wizard', function () {
    $this->artisan('ddd:config')
        ->expectsQuestion('Laravel-DDD Config Utility', 'wizard')
        ->expectsOutput('This command is not supported with your currently installed version of Laravel Prompts.')
        ->assertFailed()
        ->execute();
})->skip(fn () => ConfigCommand::hasRequiredVersionOfLaravelPrompts());

it('can update and merge ddd.php with latest package version', function () {
    $configPath = config_path('ddd.php');

    $originalContents = <<<'PHP'
<?php
return [];
PHP;

    file_put_contents($configPath, $originalContents);

    $this->artisan('ddd:config')
        ->expectsQuestion('Laravel-DDD Config Utility', 'update')
        ->expectsQuestion('Are you sure you want to update ddd.php and merge with latest copy from the package?', true)
        ->expectsOutput('Merging ddd.php...')
        ->expectsOutput("Configuration updated: {$configPath}")
        ->expectsOutput('Note: Some values may require manual adjustment.')
        ->assertSuccessful()
        ->execute();

    $packageConfigContents = file_get_contents(DDD::packagePath('config/ddd.php'));

    expect($updatedContents = file_get_contents($configPath))
        ->not->toEqual($originalContents);

    $updatedConfigArray = include $configPath;
    $packageConfigArray = include DDD::packagePath('config/ddd.php');

    expect($updatedConfigArray)->toHaveKeys(array_keys($packageConfigArray));

    unlink($configPath);
});

it('can sync composer.json from ddd.php ', function () {
    $configContent = <<<'PHP'
<?php
return [
    'domain_path' => 'src/CustomDomain',
    'domain_namespace' => 'CustomDomain',
    'application_path' => 'src/CustomApplication',
    'application_namespace' => 'CustomApplication',
    'application_objects' => [
        'controller',
        'request',
        'middleware',
    ],
    'layers' => [
        'Infrastructure' => 'src/Infrastructure',
        'CustomLayer' => 'src/CustomLayer',
    ],
];
PHP;

    file_put_contents(config_path('ddd.php'), $configContent);

    $this->artisan('config:cache')->assertSuccessful()->execute();

    $composerContents = file_get_contents(base_path('composer.json'));

    $fragments = [
        '"CustomDomain\\\\": "src/CustomDomain"',
        '"Infrastructure\\\\": "src/Infrastructure"',
        '"CustomLayer\\\\": "src/CustomLayer"',
        '"CustomApplication\\\\": "src/CustomApplication"',
    ];

    expect($composerContents)->not->toContain(...$fragments);

    $this->artisan('ddd:config')
        ->expectsQuestion('Laravel-DDD Config Utility', 'composer')
        ->expectsOutput('Syncing composer.json from ddd.php...')
        ->expectsOutputToContain(...[
            'Namespace',
            'Path',
            'Status',

            'CustomDomain',
            'src/CustomDomain',
            'Added',

            'CustomApplication',
            'src/CustomApplication',
            'Added',

            'Infrastructure',
            'src/Infrastructure',
            'Added',
        ])
        ->assertSuccessful()
        ->execute();

    $composerContents = file_get_contents(base_path('composer.json'));

    expect($composerContents)->toContain(...$fragments);

    $this->artisan('config:clear')->assertSuccessful()->execute();

    unlink(config_path('ddd.php'));
});

it('can detect domain namespace from composer.json', function () {
    $sampleComposer = file_get_contents(__DIR__.'/resources/composer.sample.json');

    file_put_contents(
        app()->basePath('composer.json'),
        $sampleComposer
    );

    $configPath = config_path('ddd.php');

    $this->artisan('ddd:config')
        ->expectsQuestion('Laravel-DDD Config Utility', 'detect')
        ->expectsOutputToContain(...[
            'Detected configuration:',
            'domain_path',
            'lib/CustomDomain',
            'domain_namespace',
            'Domain',
        ])
        ->expectsQuestion('Update configuration with these values?', true)
        ->expectsOutput('Configuration updated: '.$configPath)
        ->assertSuccessful()
        ->execute();

    $configValues = DDD::config()->get();

    expect(data_get($configValues, 'domain_path'))->toBe('lib/CustomDomain');
    expect(data_get($configValues, 'domain_namespace'))->toBe('Domain');

    unlink($configPath);
});
