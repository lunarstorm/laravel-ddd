<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

it('can generate value objects', function () {
    $valueObjectName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.value_objects'),
        "{$valueObjectName}.php"
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:make:model {$domain} {$valueObjectName}");

    expect(file_exists($expectedPath))->toBeTrue();
});

it('can generate domain models in custom domain folder', function () {
    $customDomainPath = 'Custom/Domains';

    Config::set('ddd.paths.domains', $customDomainPath);

    $valueObjectName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        $customDomainPath,
        $domain,
        config('ddd.namespaces.value_objects'),
        "{$valueObjectName}.php"
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:make:model {$domain} {$valueObjectName}");

    expect(file_exists($expectedPath))->toBeTrue();
});

it('normalizes generated model name to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.value_objects'),
        "{$normalized}.php"
    ]));

    Artisan::call("ddd:make:model {$domain} {$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with([
    'apple' => ['apple', 'Apple'],
    'Apple' => ['Apple', 'Apple'],
    'appleBottom' => ['appleBottom', 'AppleBottom'],
    'AppleBottom' => ['AppleBottom', 'AppleBottom'],
    'apple-bottom' => ['apple-bottom', 'AppleBottom'],
]);

it('generates the base model if needed', function () {
    $valueObjectName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.value_objects'),
        "{$valueObjectName}.php"
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:make:model {$domain} {$valueObjectName}");

    expect(file_exists($expectedPath))->toBeTrue();
});
