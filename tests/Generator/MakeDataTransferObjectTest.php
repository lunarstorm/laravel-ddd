<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate data transfer objects', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $dtoName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.data_transfer_objects'),
        "{$dtoName}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:dto {$domain} {$dtoName}");

    expect(Artisan::output())->ifElse(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContain("Data Transfer Object [{$relativePath}] created successfully."),
        fn ($output) => $output->toContain("Data Transfer Object created successfully."),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.data_transfer_objects'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('normalizes generated data transfer object to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.data_transfer_objects'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:dto {$domain} {$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with('makeDtoInputs');

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:dto')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->expectsQuestion('What should the data transfer object be named?', 'Belt')
        ->assertExitCode(0);
})->ifSupportsPromptForMissingInput();
