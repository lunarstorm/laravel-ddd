<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Support\Path;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->commands = [
        'invoice:deliver' => 'Domain\Invoicing\Commands\InvoiceDeliver',
        'log:prune' => 'Infrastructure\Commands\LogPrune',
        'application:sync' => 'Application\Commands\ApplicationSync',
    ];

    $this->setupTestApplication();

    DomainCache::clear();
    Artisan::call('ddd:clear');

    expect(config('ddd.autoload_ignore'))->toEqualCanonicalizing([
        'Tests',
        'Database/Migrations',
    ]);
});

afterEach(function () {
    DomainCache::clear();
    Artisan::call('ddd:clear');
});

describe('when ddd.autoload.commands = false', function () {
    it('skips handling commands', function () {
        config()->set('ddd.autoload.commands', false);

        $mock = AutoloadManager::partialMock();
        $mock->shouldNotReceive('handleCommands');
        $mock->run();

        expect($mock->getRegisteredCommands())->toBeEmpty();
    });
});

describe('when ddd.autoload.commands = true', function () {
    it('registers the commands', function () {
        config()->set('ddd.autoload.commands', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        $expected = array_values($this->commands);
        $registered = array_values($mock->getRegisteredCommands());
        expect($mock->discoverCommands())->toEqualCanonicalizing($expected);
        expect($expected)->each(fn ($item) => $item->toBeIn($registered));
        expect($registered)->toHaveCount(count($expected));
    });
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-commands', []);

        config()->set('ddd.autoload.commands', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        $registered = array_values($mock->getRegisteredCommands());
        expect($registered)->toHaveCount(0);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-commands', []);
        DomainCache::clear();

        config()->set('ddd.autoload.commands', true);

        $mock = AutoloadManager::partialMock();
        $mock->run();

        expect($mock->getAllLayerPaths())->toEqualCanonicalizing([
            Path::normalize(base_path('src/Domain')),
            Path::normalize(base_path('src/Application')),
            Path::normalize(base_path('src/Infrastructure')),
        ]);

        $expected = array_values($this->commands);
        $registered = array_values($mock->getRegisteredCommands());
        expect($mock->discoverCommands())->toEqualCanonicalizing($expected);
        expect($expected)->each(fn ($item) => $item->toBeIn($registered));
        expect($registered)->toHaveCount(count($expected));
    });
});
