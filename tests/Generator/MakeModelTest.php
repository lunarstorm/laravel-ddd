<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

it('can generate domain models', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    $relativePath = implode('/', [
        $domainPath,
        $domain,
        config('ddd.namespaces.model'),
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
        fn ($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedModelPath))->toBeTrue();

    $expectedNamespace = implode('\\', [
        $domainRoot,
        $domain,
        config('ddd.namespaces.model'),
    ]);

    expect(file_get_contents($expectedModelPath))->toContain("namespace {$expectedNamespace};");
})->with('domainPaths');

it('can generate a domain model with factory', function ($domainPath, $domainRoot, $domainName, $subdomain) {
    Config::set('ddd.domain_path', $domainPath);

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

    expect(Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn ($output) => $output->toContainFilepath($domainModel->path),
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
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.model'),
        "{$normalized}.php",
    ]));

    Artisan::call("ddd:model {$domain}:{$given}");

    expect(file_exists($expectedModelPath))->toBeTrue();
})->with('makeModelInputs');

it('generates the base model when possible', function ($baseModelClass, $baseModelPath) {
    $modelName = Str::studly(fake()->word());
    $domain = Str::studly(fake()->word());

    Config::set('ddd.base_model', $baseModelClass);

    $expectedModelPath = base_path(implode('/', [
        config('ddd.domain_path'),
        $domain,
        config('ddd.namespaces.model'),
        "{$modelName}.php",
    ]));

    $expectedModelClass = implode('\\', [
        basename(config('ddd.domain_path')),
        $domain,
        config('ddd.namespaces.model'),
        $modelName,
    ]);

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    expect(file_exists($expectedModelPath))->toBeFalse();

    $expectedBaseModelPath = base_path($baseModelPath);

    if (file_exists($expectedBaseModelPath)) {
        unlink($expectedBaseModelPath);
    }

    expect(class_exists($baseModelClass))->toBeFalse();

    expect(file_exists($expectedBaseModelPath))->toBeFalse("{$baseModelPath} expected not to exist.");

    Artisan::call("ddd:model {$domain} {$modelName}");

    expect(file_exists($expectedBaseModelPath))->toBeTrue("Expecting base model file to be generated at {$baseModelPath}");

    // Not able to properly assert the following class_exists checks under the testing environment
    // expect(class_exists($expectedModelClass))->toBeTrue("Expecting model class {$expectedModelClass} to exist");
    // expect(class_exists($baseModelClass))->toBeTrue("Expecting base model class {$baseModelClass} to exist");
})->with([
    ['Domain\Shared\Models\CustomBaseModel', 'src/Domain/Shared/Models/CustomBaseModel.php'],
    ['Domain\Core\Models\CustomBaseModel', 'src/Domain/Core/Models/CustomBaseModel.php'],
]);

it('will not generate a base model if the configured base model is out of scope', function ($baseModel) {
    Config::set('ddd.base_model', $baseModel);

    expect(class_exists($baseModel))->toBeFalse();

    Artisan::call('ddd:model Fruits Lemon');

    expect(Artisan::output())
        ->toContain("Configured base model {$baseModel} doesn't exist.")
        ->not->toContain("Generating {$baseModel}");

    expect(class_exists($baseModel))->toBeFalse();
})->with([
    ['Illuminate\Database\Eloquent\NonExistentModel'],
    ['OtherVendor\OtherPackage\Models\NonExistentModel'],
]);

it('skips base model creation if configured base model already exists', function ($baseModel) {
    Config::set('ddd.base_model', $baseModel);

    expect(class_exists($baseModel))->toBeTrue();

    Artisan::call('ddd:model Fruits Lemon');

    expect(Artisan::output())
        ->not->toContain("Configured base model {$baseModel} doesn't exist.")
        ->not->toContain("Generating {$baseModel}");
})->with([
    ['Illuminate\Database\Eloquent\Model'],
    ['Lunarstorm\LaravelDDD\Models\DomainModel'],
]);

it('shows meaningful hints when prompting for missing input', function () {
    $this->artisan('ddd:model')
        ->expectsQuestion('What is the domain?', 'Utility')
        ->expectsQuestion('What should the model be named?', 'Belt')
        ->assertExitCode(0);
});
