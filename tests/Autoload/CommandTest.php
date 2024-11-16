<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Facades\Autoload;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Lunarstorm\LaravelDDD\Tests\BootsTestApplication;
use Mockery\MockInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

uses(BootsTestApplication::class);

beforeEach(function () {
    $this->commands = [
        'invoice:deliver' => 'Domain\Invoicing\Commands\InvoiceDeliver',
        'log:prune' => 'Infrastructure\Commands\LogPrune',
        'application:sync' => 'Application\Commands\ApplicationSync',
    ];

    $this->setupTestApplication();
});

afterEach(function () {
    DomainCache::clear();
});

describe('when ddd.autoload.commands = false', function () {
    it('skips handling commands', function () {
        config()->set('ddd.autoload.commands', false);

        $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldNotReceive('handleCommands');
        });

        $mock->boot();

        expect($mock->getRegisteredCommands())->toBeEmpty();
    });

    it('does not register the commands', function () {
        config()->set('ddd.autoload.commands', false);

        Autoload::boot();

        $artisanCommands = collect(Artisan::all());

        expect($artisanCommands)->not->toHaveKeys(array_keys($this->commands));
    });
});

describe('when ddd.autoload.commands = true', function () {
    it('registers the commands', function () {
        config()->set('ddd.autoload.commands', true);

        // $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
        //     $mock->shouldAllowMockingProtectedMethods();
        // });

        // $mock->boot();

        // expect(array_values($mock->getRegisteredCommands()))->toEqualCanonicalizing(array_values($this->commands));
        Autoload::boot();

        $artisanCommands = collect(Artisan::all());

        expect($artisanCommands)->toHaveKeys(array_keys($this->commands));
    });
});

describe('caching', function () {
    it('remembers the last cached state', function () {
        DomainCache::set('domain-commands', []);

        config()->set('ddd.autoload.commands', true);

        // $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
        //     $mock->shouldAllowMockingProtectedMethods();
        // });

        // $mock->boot();

        // expect(array_values($mock->getRegisteredCommands()))->toEqualCanonicalizing([]);

        Autoload::boot();

        $artisanCommands = collect(Artisan::all());

        expect($artisanCommands)->not->toHaveKeys(array_keys($this->commands));

        // commands should not be recognized due to cached empty-state
        foreach ($this->commands as $command => $class) {
            expect(fn () => Artisan::call($command))->toThrow(CommandNotFoundException::class);
        }
    });

    it('can bust the cache', function () {
        DomainCache::set('domain-commands', []);
        DomainCache::clear();

        // $mock = $this->partialMock(AutoloadManager::class, function (MockInterface $mock) {
        //     $mock->shouldAllowMockingProtectedMethods();
        // });

        // $mock->boot();

        // expect(array_values($mock->getRegisteredCommands()))->toEqualCanonicalizing(array_values($this->commands));

        Autoload::boot();

        $artisanCommands = collect(Artisan::all());

        expect($artisanCommands)->toHaveKeys(array_keys($this->commands));

        foreach ($this->commands as $command => $class) {
            $this->artisan($command)->assertSuccessful();
        }
    });
});
