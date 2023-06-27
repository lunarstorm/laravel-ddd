<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

it('can generate domain base model', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $modelName = 'BaseModel';
    $domain = 'Shared';

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.models'),
        "{$modelName}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:base-model {$domain} {$modelName}");

    expect(Artisan::output())->toMatchRegularExpression('/Base Model (\[.*\])? created successfully./');

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.models'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:base-model')
        ->expectsQuestion('What is the domain?', 'Shared')
        ->assertExitCode(0);
})->ifSupportsPromptForMissingInput();
