<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate domain base model', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $modelName = 'BaseModel';
    $domain = 'Shared';

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.model'),
        "{$modelName}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:base-model {$domain}:{$modelName}");

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.model'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');
