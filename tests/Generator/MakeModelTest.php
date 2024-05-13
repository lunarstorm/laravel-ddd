<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;


function expectGeneratedDomainFile($output, $fileName, $domain, $type)
{
    $domainObject = $domain->$type($fileName);
    $expectedFilePath = base_path($domainObject->path);

    if (file_exists($expectedFilePath)) {
        unlink($expectedFilePath);
    }

    expect($output)->toContainFilepath($expectedFilePath);

    expect(file_exists($expectedFilePath))->toBeTrue("Expecting file to be generated at {$expectedFilePath}");

    return $expectedFilePath;
}

it('can generate domain models', function ($domainPath, $domainRoot) {
    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $modelName = 'Record';
    $domain = 'World';

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

    Artisan::call("ddd:model {$domain}:{$modelName}");

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

    $modelName = 'Record';

    $domain = new Domain($domainName, $subdomain);

    $domainModel = $domain->model($modelName);

    Artisan::call('ddd:model', [
        'name' => $modelName,
        '--domain' => $domain->dotName,
        '--factory' => true,
    ]);

    $output = Artisan::output();

    $expectedModelPath = expectGeneratedDomainFile($output, $modelName, $domain, 'model');
    $expectedFactoryPath = expectGeneratedDomainFile($output, "{$modelName}Factory", $domain, 'factory');

    expect($output)->toContainFilepath($domainModel->path);

    expect(file_exists($expectedModelPath))->toBeTrue("Expecting model file to be generated at {$expectedModelPath}");

    expect(file_get_contents($expectedFactoryPath))
        ->toContain("use {$domainModel->fullyQualifiedName};")
        ->toContain("protected \$model = {$modelName}::class;");

})->with('domainPaths')->with('domainSubdomain');

it('can generate a domain model with seeder', function ($domainPath, $domainRoot, $domainName, $subdomain) {
    Config::set('ddd.domain_path', $domainPath);

    $modelName = 'Record';

    $domain = new Domain($domainName, $subdomain);

    $domainModel = $domain->model($modelName);

    Artisan::call('ddd:model', [
        'name' => $modelName,
        '--domain' => $domain->dotName,
        '--seed' => true,
    ]);

    $output = Artisan::output();

    $expectedModelPath = expectGeneratedDomainFile($output, $modelName, $domain, 'model');
    $expectedSeederPath = expectGeneratedDomainFile($output, "{$modelName}Seeder", $domain, 'seeder');

    expect(file_exists($expectedModelPath))->toBeTrue("Expecting model file to be generated at {$expectedModelPath}");
    expect(file_exists($expectedSeederPath))->toBeTrue("Expecting seeder file to be generated at {$expectedSeederPath}");

    expect(file_get_contents($expectedSeederPath))
        ->toContain("class {$modelName}Seeder extends Seeder")
        ->toContain("public function run(): void");
})->with('domainPaths')->with('domainSubdomain');

it('can generate a domain model with migration', function ($domainPath, $domainRoot, $domainName, $subdomain) {
    Config::set('ddd.domain_path', $domainPath);

    $modelName = 'Record';

    $domain = new Domain($domainName, $subdomain);

    $migrationName = Str::snake(Str::pluralStudly(class_basename($modelName)));
    //$migrationName = "{$modelName}Factory";

    $domainModel = $domain->model($modelName);

    $domainMigration = $domain->migration($migrationName);

    $expectedModelPath = base_path($domainModel->path);

    if (file_exists($expectedModelPath)) {
        unlink($expectedModelPath);
    }

    $expectedMigrationPath = base_path($domainMigration->path);

    if (file_exists($expectedMigrationPath)) {
        unlink($expectedMigrationPath);
    }

    Artisan::call('ddd:model', [
        'name' => $modelName,
        '--domain' => $domain->dotName,
        '--migration' => true,
    ]);

    $output = Artisan::output();

    //dd(scandir(dirname(base_path($expectedMigrationPath))));

    expect($output)->toContainFilepath($domainModel->path);

    expect(file_exists($expectedModelPath))->toBeTrue("Expecting model file to be generated at {$expectedModelPath}");
    expect(file_exists($expectedMigrationPath))->toBeTrue("Expecting factory file to be generated at {$expectedMigrationPath}");

    expect(file_get_contents($expectedMigrationPath))
        ->toContain("use {$domainModel->fullyQualifiedName};")
        ->toContain("protected \$model = {$modelName}::class;");
})->with('domainPaths')->with('domainSubdomain');



it('can generate a domain model with all', function ($domainPath, $domainRoot, $domainName, $subdomain) {
    Config::set('ddd.domain_path', $domainPath);

    $modelName = 'Record';

    $domain = new Domain($domainName, $subdomain);

    $domainModel = $domain->model($modelName);

    Artisan::call('ddd:model', [
        'name' => $modelName,
        '--domain' => $domain->dotName,
        '--all' => true,
    ]);

    $output = Artisan::output();

    $expectedModelPath = expectGeneratedDomainFile($output, $modelName, $domain, 'model');
    $expectedFactoryPath = expectGeneratedDomainFile($output, "{$modelName}Factory", $domain, 'factory');
    $expectedSeederPath = expectGeneratedDomainFile($output, "{$modelName}Seeder", $domain, 'seeder');

    expect($output)->toContainFilepath($domainModel->path);

    expect(file_exists($expectedModelPath))->toBeTrue("Expecting model file to be generated at {$expectedModelPath}");
    expect(file_exists($expectedFactoryPath))->toBeTrue("Expecting factory file to be generated at {$expectedFactoryPath}");

    expect(file_get_contents($expectedFactoryPath))
        ->toContain("use {$domainModel->fullyQualifiedName};")
        ->toContain("protected \$model = {$modelName}::class;");
})->with('domainPaths')->with('domainSubdomain');

it('normalizes generated model to pascal case', function ($given, $normalized) {
    $domain = 'World';

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
    $modelName = 'Record';
    $domain = 'World';

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

    Artisan::call("ddd:model {$domain}:{$modelName}");

    expect(file_exists($expectedBaseModelPath))->toBeTrue("Expecting base model file to be generated at {$baseModelPath}");

    // Not able to properly assert the following class_exists checks under the testing environment
    // expect(class_exists($expectedModelClass))->toBeTrue("Expecting model class {$expectedModelClass} to exist");
    // expect(class_exists($baseModelClass))->toBeTrue("Expecting base model class {$baseModelClass} to exist");
})->with([
    ['Domain\Shared\Models\CustomBaseModel', 'src/Domain/Shared/Models/CustomBaseModel.php'],
    ['Domain\Core\Models\CustomBaseModel', 'src/Domain/Core/Models/CustomBaseModel.php'],
    ['Domain\Core\BaseModels\CustomBaseModel', 'src/Domain/Core/BaseModels/CustomBaseModel.php'],
]);

it('will not generate a base model if the configured base model is out of scope', function ($baseModel) {
    Config::set('ddd.base_model', $baseModel);

    expect(class_exists($baseModel))->toBeFalse();

    Artisan::call('ddd:model Fruits:Lemon');

    expect(Artisan::output())->not->toContain("Configured base model {$baseModel} doesn't exist, generating...");

    expect(class_exists($baseModel))->toBeFalse();
})->with([
    ['Illuminate\Database\Eloquent\NonExistentModel'],
    ['OtherVendor\OtherPackage\Models\NonExistentModel'],
]);

it('skips base model creation if configured base model already exists', function ($baseModel) {
    Config::set('ddd.base_model', $baseModel);

    expect(class_exists($baseModel))->toBeTrue();

    Artisan::call('ddd:model Fruits:Lemon');

    expect(Artisan::output())->not->toContain("Configured base model {$baseModel} doesn't exist, generating...");
})->with([
    ['Illuminate\Database\Eloquent\Model'],
    ['Lunarstorm\LaravelDDD\Models\DomainModel'],
]);
