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
    $this->setupTestApplication();
});

it('can ignore folders when autoloading', function () {
    Artisan::call('ddd:optimize');

    $expected = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Domain\Invoicing\Commands\InvoiceDeliver',
        'Application\Providers\ApplicationServiceProvider',
        'Application\Commands\ApplicationSync',
        'Infrastructure\Providers\InfrastructureServiceProvider',
        'Infrastructure\Commands\LogPrune',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqualCanonicalizing($expected);

    Config::set('ddd.autoload_ignore', ['Commands']);

    Artisan::call('ddd:optimize');

    $expected = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Application\Providers\ApplicationServiceProvider',
        'Infrastructure\Providers\InfrastructureServiceProvider',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqualCanonicalizing($expected);

    Config::set('ddd.autoload_ignore', ['Providers']);

    Artisan::call('ddd:optimize');

    $expected = [
        'Domain\Invoicing\Commands\InvoiceDeliver',
        'Application\Commands\ApplicationSync',
        'Infrastructure\Commands\LogPrune',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqual($expected);
});

it('can register a custom autoload filter', function () {
    Artisan::call('ddd:optimize');

    $expected = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Domain\Invoicing\Commands\InvoiceDeliver',
        'Application\Providers\ApplicationServiceProvider',
        'Application\Commands\ApplicationSync',
        'Infrastructure\Providers\InfrastructureServiceProvider',
        'Infrastructure\Commands\LogPrune',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqualCanonicalizing($expected);

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

    Artisan::call('ddd:optimize');

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqual([]);

    expect($secret)->toEqual('i-was-invoked');
});
