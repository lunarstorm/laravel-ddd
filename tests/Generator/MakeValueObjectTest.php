<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

it('can generate value objects', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $valueObjectName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        $domainPath,
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

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.value_objects'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

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

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan("ddd:value")
        ->expectsQuestion('What is the domain?', 'Utility')
        ->expectsQuestion('What should the value object be named?', 'Belt')
        ->assertExitCode(0);
});
