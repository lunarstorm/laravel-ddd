<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate value objects', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $domain = 'Mission';
    $valueObjectName = 'ImpossibleValue';

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.value_object'),
        "{$valueObjectName}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:value {$domain}:{$valueObjectName}");

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.value_object'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('normalizes generated value object to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.value_object'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:value {$domain}:{$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with([
    'number' => ['number', 'Number'],
    'Number' => ['Number', 'Number'],
    'largeNumber' => ['largeNumber', 'LargeNumber'],
    'LargeNumber' => ['LargeNumber', 'LargeNumber'],
    'large-number' => ['large-number', 'LargeNumber'],
]);
