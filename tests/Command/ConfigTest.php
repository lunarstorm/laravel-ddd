<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Facades\DDD;

beforeEach(function () {
    $this->cleanSlate();
    $this->setupTestApplication();
    Artisan::call('config:clear');
    $this->composerReload();
})->skip();

afterEach(function () {
    $this->cleanSlate();
    Artisan::call('config:clear');
    $this->composerReload();
});

it('can run the config wizard', function () {
    Artisan::call('config:cache');

    expect(config('ddd.domain_path'))->toBe('src/Domain');
    expect(config('ddd.domain_namespace'))->toBe('Domain');
    expect(config('ddd.layers'))->toBe([
        'Infrastructure' => 'src/Infrastructure',
    ]);

    $path = config_path('ddd.php');

    $this->artisan('ddd:config')
        ->expectsQuestion('Laravel-DDD Config Utility', 'wizard')
        ->expectsQuestion('Domain Path', 'src/CustomDomain')
        ->expectsQuestion('Domain Namespace', 'CustomDomain')
        ->expectsQuestion('Path to Application Layer', null)
        ->expectsQuestion('Custom Layers (Optional)', ['Support' => 'src/Support'])
        ->expectsOutput('Building configuration...')
        ->expectsOutput("Configuration updated: {$path}")
        ->assertSuccessful()
        ->execute();

    expect(file_exists($path))->toBeTrue();

    Artisan::call('config:cache');

    expect(config('ddd.domain_path'))->toBe('src/CustomDomain');
    expect(config('ddd.domain_namespace'))->toBe('CustomDomain');
    expect(config('ddd.application'))->toBe([
        'path' => 'app/Modules',
        'namespace' => 'App\Modules',
        'objects' => [
            'controller',
            'request',
            'middleware',
        ],
    ]);
    expect(config('ddd.layers'))->toBe([
        'Support' => 'src/Support',
    ]);
});

it('can update and merge ddd.php with latest package version', function () {
    $path = config_path('ddd.php');

    $originalContents = <<<'PHP'
<?php
return [];
PHP;

    file_put_contents($path, $originalContents);

    $this->artisan('ddd:config')
        ->expectsQuestion('Laravel-DDD Config Utility', 'update')
        ->expectsQuestion('Are you sure you want to update ddd.php and merge with latest copy from the package?', true)
        ->expectsOutput('Merging ddd.php...')
        ->expectsOutput("Configuration updated: {$path}")
        ->expectsOutput('Note: Some values may require manual adjustment.')
        ->assertSuccessful()
        ->execute();

    $packageConfigContents = file_get_contents(DDD::packagePath('config/ddd.php'));

    expect($updatedContents = file_get_contents($path))
        ->not->toEqual($originalContents);

    $updatedConfigArray = include $path;
    $packageConfigArray = include DDD::packagePath('config/ddd.php');

    expect($updatedConfigArray)->toHaveKeys(array_keys($packageConfigArray));
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

    Artisan::call('config:cache');

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
});

it('can detect domain namespace from composer.json', function () {
    $sampleComposer = file_get_contents(__DIR__.'/resources/composer.sample.json');

    file_put_contents(
        app()->basePath('composer.json'),
        $sampleComposer
    );

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
        ->expectsOutput('Configuration updated: '.config_path('ddd.php'))
        ->assertSuccessful()
        ->execute();

    $configValues = DDD::config()->get();

    expect(data_get($configValues, 'domain_path'))->toBe('lib/CustomDomain');
    expect(data_get($configValues, 'domain_namespace'))->toBe('Domain');
});
