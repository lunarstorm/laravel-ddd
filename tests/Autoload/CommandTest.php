<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\Support\DomainAutoloader;
use Symfony\Component\Console\Exception\CommandNotFoundException;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    $this->setupTestApplication();

    DomainAutoloader::clearCache();
});

describe('without autoload', function () {
    it('does not register the command', function () {
        expect(class_exists('Domain\Invoicing\Commands\InvoiceDeliver'))->toBeTrue();
        expect(fn () => Artisan::call('invoice:deliver'))->toThrow(CommandNotFoundException::class);
    });
});

describe('with autoload', function () {
    beforeEach(function () {
        $this->afterApplicationCreated(function () {
            (new DomainAutoloader())->autoload();
        });
    });

    it('registers the command', function () {
        expect(class_exists('Domain\Invoicing\Commands\InvoiceDeliver'))->toBeTrue();
        Artisan::call('invoice:deliver');
        expect(Artisan::output())->toContain('Invoice delivered!');
    });

    it('recognizes new commands created afterwards', function () {
        expect(class_exists('Domain\Invoicing\Commands\InvoiceVoid'))->toBeFalse();

        Artisan::call('ddd:command', [
            'name' => 'InvoiceVoid',
            '--domain' => 'Invoicing',
        ]);

        $filepath = base_path('src/Domain/Invoicing/Commands/InvoiceVoid.php');

        expect(file_exists($filepath))->toBeTrue();

        $class = 'Domain\Invoicing\Commands\InvoiceVoid';

        // dd(
        //     [
        //         // pre-created files work fine
        //         'App\Models\User' => [
        //             'path' => base_path('app/Models/User.php'),
        //             'file_exists' => file_exists(base_path('app/Models/User.php')),
        //             'class_exists' => class_exists('App\Models\User'),
        //         ],

        //         'Domain\Invoicing\Models\Invoice' => [
        //             'path' => base_path('src/Domain/Invoicing/Models/Invoice.php'),
        //             'file_exists' => file_exists(base_path('src/Domain/Invoicing/Models/Invoice.php')),
        //             'class_exists' => class_exists('Domain\Invoicing\Models\Invoice'),
        //         ],

        //         // but runtime-created class created but not recognized by class_exists
        //         $class => [
        //             'path' => $filepath,
        //             'file_exists' => file_exists($filepath),
        //             'class_exists' => class_exists($class),
        //         ],
        //     ],
        // );

        $instance = new $class();

        expect(class_exists($class))->toBeTrue();
    })->markTestIncomplete("Can't get this to work under test environment");
});
