<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate base view model', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $className = 'ViewModel';
    $domain = 'Shared';

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.view_models'),
        "{$className}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:base-view-model {$domain} {$className}");

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

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:base-view-model')
        ->expectsQuestion('What is the domain?', 'Shared')
        ->assertExitCode(0);
})->ifSupportsPromptForMissingInput();
