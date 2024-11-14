<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Symfony\Component\Console\Exception\CommandNotFoundException;

beforeEach(function () {
    Config::set([
        'ddd.domain_path' => 'src/Domain',
        'ddd.domain_namespace' => 'Domain',
        'ddd.application_namespace' => 'Application',
        'ddd.application_path' => 'src/Application',
        'ddd.application_objects' => [
            'controller',
            'request',
            'middleware',
        ],
        'ddd.layers' => [
            'Infrastructure' => 'src/Infrastructure',
        ],
    ]);
});

describe('without autoload', function () {
    beforeEach(function () {
        Config::set('ddd.autoload.commands', false);

        $this->setupTestApplication();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader)->autoload();
        });
    });

    it('does not register the command', function ($className, $command) {
        expect(class_exists($className))->toBeTrue();
        expect(fn () => Artisan::call($command))->toThrow(CommandNotFoundException::class);
    })->with([
        ['Domain\Invoicing\Commands\InvoiceDeliver', 'invoice:deliver'],
        ['Infrastructure\Commands\LogPrune', 'log:prune'],
        ['Application\Commands\ApplicationSync', 'application:sync'],
    ]);
});

describe('with autoload', function () {
    beforeEach(function () {
        Config::set('ddd.autoload.commands', true);

        $this->setupTestApplication();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader)->autoload();
        });
    });

    it('registers existing commands', function ($className, $command, $output) {
        expect(collect(Artisan::all()))
            ->has($command)
            ->toBeTrue();

        expect(class_exists($className))->toBeTrue();
        Artisan::call($command);
        expect(Artisan::output())->toContain($output);
    })->with([
        ['Domain\Invoicing\Commands\InvoiceDeliver', 'invoice:deliver', 'Invoice delivered!'],
        ['Infrastructure\Commands\LogPrune', 'log:prune', 'System logs pruned!'],
        ['Application\Commands\ApplicationSync', 'application:sync', 'Application state synced!'],
    ]);

    it('registers newly created commands', function () {
        $command = 'app:invoice-void';

        expect(collect(Artisan::all()))
            ->has($command)
            ->toBeFalse();

        Artisan::call('ddd:command', [
            'name' => 'InvoiceVoid',
            '--domain' => 'Invoicing',
        ]);

        expect(collect(Artisan::all()))
            ->has($command)
            ->toBeTrue();

        $this->artisan($command)->assertSuccessful();
    })->skip("Can't get this to work, might not be test-able without a real app environment.");
});

describe('caching', function () {
    beforeEach(function () {
        Config::set('ddd.autoload.commands', true);

        $this->setupTestApplication();
    });

    it('remembers the last cached state', function () {
        DomainCache::set('domain-commands', []);

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader)->autoload();
        });

        // command should not be recognized due to cached empty-state
        expect(fn () => Artisan::call('invoice:deliver'))->toThrow(CommandNotFoundException::class);
        expect(fn () => Artisan::call('log:prune'))->toThrow(CommandNotFoundException::class);
        expect(fn () => Artisan::call('application:sync'))->toThrow(CommandNotFoundException::class);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-commands', []);
        DomainCache::clear();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader)->autoload();
        });

        $this->artisan('invoice:deliver')->assertSuccessful();
        $this->artisan('log:prune')->assertSuccessful();
        $this->artisan('application:sync')->assertSuccessful();
    });
});
