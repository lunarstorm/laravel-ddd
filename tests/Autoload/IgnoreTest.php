<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Support\Path;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;
use Symfony\Component\Finder\SplFileInfo;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->providers = [
        'Application\Providers\ApplicationServiceProvider',
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Infrastructure\Providers\InfrastructureServiceProvider',
    ];

    $this->commands = [
        'application:sync' => 'Application\Commands\ApplicationSync',
        'invoice:deliver' => 'Domain\Invoicing\Commands\InvoiceDeliver',
        'log:prune' => 'Infrastructure\Commands\LogPrune',
    ];

    $this->setupTestApplication();

    DomainCache::clear();
    Artisan::call('ddd:clear');

    Config::set('ddd.autoload', [
        'providers' => true,
        'commands' => true,
        'factories' => true,
        'policies' => true,
        'migrations' => true,
    ]);

    expect(DDD::autoloader()->getAllLayerPaths())->toEqualCanonicalizing([
        Path::normalize(base_path('src/Domain')),
        Path::normalize(base_path('src/Application')),
        Path::normalize(base_path('src/Infrastructure')),
    ]);

    expect(config('ddd.autoload_ignore'))->toEqualCanonicalizing([
        'Tests',
        'Database/Migrations',
    ]);

    foreach ($this->providers as $provider) {
        expect(class_exists($provider))->toBeTrue("{$provider} class does not exist");
    }

    foreach ($this->commands as $command) {
        expect(class_exists($command))->toBeTrue("{$command} class does not exist");
    }
});

afterEach(function () {
    DomainCache::clear();
    Artisan::call('ddd:clear');
});

it('can ignore folders when autoloading', function () {
    expect(config('ddd.domain_path'))->toEqual('src/Domain');
    expect(config('ddd.domain_namespace'))->toEqual('Domain');
    expect(config('ddd.application_path'))->toEqual('src/Application');
    expect(config('ddd.application_namespace'))->toEqual('Application');
    expect(config('ddd.layers'))->toContain('src/Infrastructure');

    $expected = [
        ...array_values($this->providers),
        ...array_values($this->commands),
    ];

    $discovered = [
        ...DDD::autoloader()->discoverProviders(),
        ...DDD::autoloader()->discoverCommands(),
    ];

    expect($expected)->each(fn ($item) => $item->toBeIn($discovered));
    expect($discovered)->toHaveCount(count($expected));

    Config::set('ddd.autoload_ignore', ['Commands']);

    $expected = [
        ...array_values($this->providers),
    ];

    $discovered = [
        ...DDD::autoloader()->discoverProviders(),
        ...DDD::autoloader()->discoverCommands(),
    ];

    expect($expected)->each(fn ($item) => $item->toBeIn($discovered));
    expect($discovered)->toHaveCount(count($expected));

    Config::set('ddd.autoload_ignore', ['Providers']);

    $expected = [
        ...array_values($this->commands),
    ];

    $discovered = [
        ...DDD::autoloader()->discoverProviders(),
        ...DDD::autoloader()->discoverCommands(),
    ];

    expect($expected)->each(fn ($item) => $item->toBeIn($discovered));
    expect($discovered)->toHaveCount(count($expected));
});

it('can register a custom autoload filter', function () {
    $expected = [
        ...array_values($this->providers),
        ...array_values($this->commands),
    ];

    $discovered = [
        ...DDD::autoloader()->discoverProviders(),
        ...DDD::autoloader()->discoverCommands(),
    ];

    expect($expected)->each(fn ($item) => $item->toBeIn($discovered));
    expect($discovered)->toHaveCount(count($expected));

    $secret = null;

    DDD::filterAutoloadPathsUsing(function (SplFileInfo $file) use (&$secret) {
        $ignoredFiles = [
            'InvoiceServiceProvider.php',
            'InvoiceDeliver.php',
            'ApplicationServiceProvider.php',
            'ApplicationSync.php',
            'InfrastructureServiceProvider.php',
            'LogPrune.php',
        ];

        $secret = 'i-was-invoked';

        if (Str::endsWith($file->getRelativePathname(), $ignoredFiles)) {
            return false;
        }
    });

    $discovered = [
        ...DDD::autoloader()->discoverProviders(),
        ...DDD::autoloader()->discoverCommands(),
    ];

    expect($discovered)->toHaveCount(0);

    expect($secret)->toEqual('i-was-invoked');
});
