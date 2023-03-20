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
        "{$valueObjectName}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:value {$domain} {$valueObjectName}");

    expect(file_exists($expectedPath))->toBeTrue();
});

it('can generate value objects in custom domain folder', function () {
    $customDomainPath = 'Custom/Domains';

    Config::set('ddd.paths.domains', $customDomainPath);

    $valueObjectName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        $customDomainPath,
        $domain,
        config('ddd.namespaces.value_objects'),
        "{$valueObjectName}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:value {$domain} {$valueObjectName}");

    expect(file_exists($expectedPath))->toBeTrue();
});

it('normalizes generated value object to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.value_objects'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:value {$domain} {$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with([
    'number' => ['number', 'Number'],
    'Number' => ['Number', 'Number'],
    'largeNumber' => ['largeNumber', 'LargeNumber'],
    'LargeNumber' => ['LargeNumber', 'LargeNumber'],
    'large-number' => ['large-number', 'LargeNumber'],
]);
