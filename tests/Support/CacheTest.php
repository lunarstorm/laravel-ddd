<?php

use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\Support\DomainCache;

beforeEach(function () {
    $cacheDirectory = config('ddd.cache_directory', 'bootstrap/cache/ddd');
    File::delete(glob(base_path("{$cacheDirectory}/ddd-*.php")));
});

it('can cache', function ($key, $value) {
    expect(DomainCache::has($key))->toBeFalse();

    DomainCache::set($key, $value);

    expect(DomainCache::has($key))->toBeTrue();

    expect(DomainCache::get($key))->toEqual($value);
})->with([
    ['value', 'ddd'],
    ['number', 123],
    ['array', [12, 23, 34]],
]);

it('can clear cache', function () {
    DomainCache::set('one', [12, 23, 34]);
    DomainCache::set('two', [45, 56, 67]);
    DomainCache::set('three', [45, 56, 67]);

    expect(DomainCache::has('one'))->toBeTrue();
    expect(DomainCache::has('two'))->toBeTrue();
    expect(DomainCache::has('three'))->toBeTrue();

    DomainCache::clear();

    expect(DomainCache::has('one'))->toBeFalse();
    expect(DomainCache::has('two'))->toBeFalse();
    expect(DomainCache::has('three'))->toBeFalse();
});
