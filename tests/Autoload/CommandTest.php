<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;
use Symfony\Component\Console\Exception\CommandNotFoundException;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->setupTestApplication();
});

afterEach(function () {
    DomainCache::clear();
});

describe('without autoload', function () {
    it('does not register the command', function ($className, $command) {
        $this->refreshApplicationWithConfig([
            'ddd.autoload.commands' => false,
        ]);

        expect(class_exists($className))->toBeTrue();
        expect(fn () => Artisan::call($command))->toThrow(CommandNotFoundException::class);
    })->with([
        ['Domain\Invoicing\Commands\InvoiceDeliver', 'invoice:deliver'],
        ['Infrastructure\Commands\LogPrune', 'log:prune'],
        ['Application\Commands\ApplicationSync', 'application:sync'],
    ]);
});

describe('with autoload', function () {
    it('registers existing commands', function ($className, $command, $output) {
        $this->afterApplicationCreated(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.commands' => true,
        ]);

        expect(class_exists($className))->toBeTrue();

        expect(collect(Artisan::all()))
            ->has($command)
            ->toBeTrue();

        Artisan::call($command);
        expect(Artisan::output())->toContain($output);
    })->with([
        ['Domain\Invoicing\Commands\InvoiceDeliver', 'invoice:deliver', 'Invoice delivered!'],
        ['Infrastructure\Commands\LogPrune', 'log:prune', 'System logs pruned!'],
        ['Application\Commands\ApplicationSync', 'application:sync', 'Application state synced!'],
    ]);
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-commands', []);

        $this->afterApplicationCreated(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.commands' => true,
        ]);

        // commands should not be recognized due to cached empty-state
        expect(fn () => Artisan::call('invoice:deliver'))->toThrow(CommandNotFoundException::class);
        expect(fn () => Artisan::call('log:prune'))->toThrow(CommandNotFoundException::class);
        expect(fn () => Artisan::call('application:sync'))->toThrow(CommandNotFoundException::class);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-commands', []);
        DomainCache::clear();

        $this->afterApplicationCreated(function () {
            app('ddd.autoloader')->boot();
        });

        $this->refreshApplicationWithConfig([
            'ddd.autoload.commands' => true,
        ]);

        $this->artisan('invoice:deliver')->assertSuccessful();
        $this->artisan('log:prune')->assertSuccessful();
        $this->artisan('application:sync')->assertSuccessful();
    });
});
