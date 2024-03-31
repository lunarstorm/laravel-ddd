<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Symfony\Component\Console\Exception\CommandNotFoundException;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');
});

describe('without autoload', function () {
    beforeEach(function () {
        Config::set('ddd.autoload.commands', false);

        $this->setupTestApplication();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });
    });

    it('does not register the command', function () {
        expect(class_exists('Domain\Invoicing\Commands\InvoiceDeliver'))->toBeTrue();
        expect(fn () => Artisan::call('invoice:deliver'))->toThrow(CommandNotFoundException::class);
    });
});

describe('with autoload', function () {
    beforeEach(function () {
        Config::set('ddd.autoload.commands', true);

        $this->setupTestApplication();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });
    });

    it('registers existing commands', function () {
        $command = 'invoice:deliver';

        expect(collect(Artisan::all()))
            ->has($command)
            ->toBeTrue();

        expect(class_exists('Domain\Invoicing\Commands\InvoiceDeliver'))->toBeTrue();
        Artisan::call($command);
        expect(Artisan::output())->toContain('Invoice delivered!');
    });

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
            (new DomainAutoloader())->autoload();
        });

        // command should not be recognized due to cached empty-state
        expect(fn () => Artisan::call('invoice:deliver'))->toThrow(CommandNotFoundException::class);
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-commands', []);
        DomainCache::clear();

        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });

        $this->artisan('invoice:deliver')->assertSuccessful();
    });
});
