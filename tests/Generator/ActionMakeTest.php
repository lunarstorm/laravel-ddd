<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate action objects', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $name = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.action'),
        "{$name}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:action {$domain}:{$name}");

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.action'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('normalizes generated action object to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.action'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:action {$domain}:{$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with('makeActionInputs');

it('extends a base action if specified in config', function ($baseAction) {
    Config::set('ddd.base_action', $baseAction);

    $name = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.action'),
        "{$name}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    Artisan::call("ddd:action {$domain}:{$name}");

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))->toContain("class {$name} extends {$baseAction}".PHP_EOL.'{');
})->with([
    'BaseAction' => 'BaseAction',
    'Base\Action' => 'Base\Action',
]);

it('does not extend a base action if not specified in config', function () {
    Config::set('ddd.base_action', null);

    $name = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.action'),
        "{$name}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    Artisan::call("ddd:action {$domain}:{$name}");

    expect(file_exists($expectedPath))->toBeTrue();
    expect(file_get_contents($expectedPath))->toContain("class {$name}".PHP_EOL.'{');
});
