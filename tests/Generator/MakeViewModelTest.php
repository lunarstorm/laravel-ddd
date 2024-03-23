<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate view models', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $viewModelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.view_models'),
        "{$viewModelName}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:view-model {$domain} {$viewModelName}");

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.view_models'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('normalizes generated view model to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.view_models'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:view-model {$domain} {$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with('makeViewModelInputs');

it('generates the base view model if needed', function () {
    $className = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.view_models'),
        "{$className}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    // This currently only tests for the default base model
    $expectedBaseViewModelPath = base_path(config('ddd.domain_path').'/Shared/ViewModels/ViewModel.php');

    if (file_exists($expectedBaseViewModelPath)) {
        unlink($expectedBaseViewModelPath);
    }

    expect(file_exists($expectedBaseViewModelPath))->toBeFalse();

    Artisan::call("ddd:view-model {$domain} {$className}");

    expect(file_exists($expectedBaseViewModelPath))->toBeTrue();
});

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:view-model')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->expectsQuestion('What should the view model be named?', 'Belt')
        ->assertExitCode(0);
})->ifSupportsPromptForMissingInput();
