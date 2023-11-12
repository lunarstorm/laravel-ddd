<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate domain models', function ($domainPath, $domainRoot) {
    Config::set('ddd.paths.domains', $domainPath);

    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.models'),
        "{$modelName}.php",
    ]);

    $expectedModelPath = base_path($relativePath);

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    expect(file_exists($expectedModelPath))->toBeFalse();

    Artisan::call("ddd:model {$domain} {$modelName}");

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContain($relativePath),
    );

    expect(file_exists($expectedModelPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.models'),
    ]);

    expect(file_get_contents($expectedModelPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('can generate a domain model with factory', function ($domainPath, $domainRoot, $domainName, $subdomain) {
    Config::set('ddd.paths.domains', $domainPath);

    $modelName = Str::studly(fake()->word());

    $domain = new Domain($domainName, $subdomain);

    $factoryName = "{$modelName}Factory";

    $domainModel = $domain->model($modelName);

    $domainFactory = $domain->factory($factoryName);

    $expectedModelPath = base_path($domainModel->path);

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    $expectedFactoryPath = base_path($domainFactory->path);

    if (file_exists($expectedFactoryPath)) {
        unlink($expectedFactoryPath);
    }

    Artisan::call('ddd:model', [
        'domain' => $domain->dotName,
        'name' => $modelName,
        '--factory' => true,
    ]);

    $outputPath = str_replace('\\', '/', $domainModel->path);

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContain($outputPath),
    );

    expect(file_exists($expectedModelPath))->toBeTrue();
    expect(file_exists($expectedFactoryPath))->toBeTrue();

    expect(file_get_contents($expectedFactoryPath))
        ->toContain("use {$domainModel->fqn};")
        ->toContain("protected \$model = {$modelName}::class;");
})->with('domainPaths')->with('domainSubdomain');

it('normalizes generated model to pascal case', function ($given, $normalized) {
    $domain = Str::studly(fake()->word());

    $expectedModelPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.models'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:model {$domain} {$given}");

    expect(file_exists($expectedModelPath))->toBeTrue();
})->with('makeModelInputs');

it('generates the base model when possible', function () {
    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $expectedModelPath = base_path(implode('/', [
        config('ddd.paths.domains'),
        $domain,
        config('ddd.namespaces.models'),
        "{$modelName}.php",
    ]));

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    expect(file_exists($expectedModelPath))->toBeFalse();

    // This currently only tests for the default base model
    $expectedBaseModelPath = base_path(config('ddd.paths.domains').'/Shared/Models/BaseModel.php');

    if (file_exists($expectedBaseModelPath)) {
        unlink($expectedBaseModelPath);
    }

    // Todo: should bypass base model creation if
    // a custom base model is being used.
    // $baseModel = config('ddd.base_model');

    expect(file_exists($expectedBaseModelPath))->toBeFalse();

    Artisan::call("ddd:model {$domain} {$modelName}");

    expect(file_exists($expectedBaseModelPath))->toBeTrue();
});

it('skips base model creation if configured base model already exists', function ($baseModel) {
    Config::set('ddd.base_model', $baseModel);

    expect(class_exists($baseModel))->toBeTrue();

    Artisan::call('ddd:model Fruits Lemon');

    expect(Artisan::output())->not->toContain("Base model {$baseModel} doesn't exist, generating...");
})->with([
    ['Illuminate\Database\Eloquent\Model'],
    ['Lunarstorm\LaravelDDD\Models\DomainModel'],
]);

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:model')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->expectsQuestion('What should the model be named?', 'Belt')
        ->assertExitCode(0);
})->ifSupportsPromptForMissingInput();
