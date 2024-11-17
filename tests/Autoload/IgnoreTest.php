<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;
use Symfony\Component\Finder\SplFileInfo;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->providers = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Application\Providers\ApplicationServiceProvider',
        'Infrastructure\Providers\InfrastructureServiceProvider',
    ];

    $this->commands = [
        'invoice:deliver' => 'Domain\Invoicing\Commands\InvoiceDeliver',
        'log:prune' => 'Infrastructure\Commands\LogPrune',
        'application:sync' => 'Application\Commands\ApplicationSync',
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
});

afterEach(function () {
    DomainCache::clear();
    Artisan::call('ddd:clear');
});

it('can ignore folders when autoloading', function () {
    $expected = [
        ...array_values($this->providers),
        ...array_values($this->commands),
    ];

    $discovered = [
        ...DDD::autoloader()->discoverProviders(),
        ...DDD::autoloader()->discoverCommands(),
    ];

    expect($discovered)->toEqualCanonicalizing($expected);

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
