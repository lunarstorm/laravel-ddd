<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Symfony\Component\Finder\SplFileInfo;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');
    $this->setupTestApplication();
});

it('can ignore folders when autoloading', function () {
    Artisan::call('ddd:cache');

    $expected = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Domain\Invoicing\Commands\InvoiceDeliver',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqual($expected);

    Config::set('ddd.autoload_ignore', ['Commands']);

    Artisan::call('ddd:cache');

    $expected = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqual($expected);

    Config::set('ddd.autoload_ignore', ['Providers']);

    Artisan::call('ddd:cache');

    $expected = [
        'Domain\Invoicing\Commands\InvoiceDeliver',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqual($expected);
});

it('can register a custom autoload filter', function () {
    Artisan::call('ddd:cache');

    $expected = [
        'Domain\Invoicing\Providers\InvoiceServiceProvider',
        'Domain\Invoicing\Commands\InvoiceDeliver',
    ];

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqual($expected);

    $secret = null;

    DDD::filterAutoloadPathsUsing(function (SplFileInfo $file) use (&$secret) {
        $ignoredFiles = [
            'InvoiceServiceProvider.php',
            'InvoiceDeliver.php',
        ];

        $secret = 'i-was-invoked';

        if (Str::endsWith($file->getRelativePathname(), $ignoredFiles)) {
            return false;
        }
    });

    Artisan::call('ddd:cache');

    $cached = [
        ...DomainCache::get('domain-providers'),
        ...DomainCache::get('domain-commands'),
    ];

    expect($cached)->toEqual([]);

    expect($secret)->toEqual('i-was-invoked');
});
