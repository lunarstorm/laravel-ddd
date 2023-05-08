<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

it('can generate base view model', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $className = 'ViewModel';
    $domain = 'Shared';

    $expectedPath = base_path(implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.view_models'),
        "{$className}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:base-view-model {$domain} {$className}");

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
