<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
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
        $mock->boot();

        expect($mock->getRegisteredCommands())->toBeEmpty();
    });
});

describe('when ddd.autoload.commands = true', function () {
    it('registers the commands', function () {
        config()->set('ddd.autoload.commands', true);

        $mock = AutoloadManager::partialMock();
        $mock->boot();

        expect(array_values($mock->getRegisteredCommands()))->toEqualCanonicalizing(array_values($this->commands));
    });
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-commands', []);

        config()->set('ddd.autoload.commands', true);

        $mock = AutoloadManager::partialMock();
        $mock->boot();

        expect(array_values($mock->getRegisteredCommands()))->toEqualCanonicalizing([]);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-commands', []);
        DomainCache::clear();

        $mock = AutoloadManager::partialMock();
        $mock->boot();

        expect(array_values($mock->getRegisteredCommands()))->toEqualCanonicalizing(array_values($this->commands));
    });
});
