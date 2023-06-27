<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate action objects', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $name = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.actions'),
        "{$name}.php",
    ]);

    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:action {$domain} {$name}");

    expect(Artisan::output())->ifElse(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContain("Action [{$relativePath}] created successfully."),
        fn ($output) => $output->toContain('Action created successfully.'),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.actions'),
    ]);

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('normalizes generated action object to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.actions'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:action {$domain} {$given}");

    expect(file_exists($expectedPath))->toBeTrue();
})->with('makeActionInputs');

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:action')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->expectsQuestion('What should the action be named?', 'DoThatThing')
        ->assertExitCode(0);
})->ifSupportsPromptForMissingInput();

it('extends a base action if specified in config', function ($baseAction) {
    Config::set('ddd.base_action', $baseAction);

    $name = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.actions'),
        "{$name}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    Artisan::call("ddd:action {$domain} {$name}");

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
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.actions'),
        "{$name}.php",
    ]));

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    Artisan::call("ddd:action {$domain} {$name}");

    expect(file_exists($expectedPath))->toBeTrue();
    expect(file_get_contents($expectedPath))->toContain("class {$name}".PHP_EOL.'{');
});
