<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\Path;

it('can override configured object namespace by using absolute dot-path', function ($type, $nameInput, $expectedNamespace, $expectedPath) {
    if (in_array($type, ['class', 'enum', 'interface', 'trait'])) {
        skipOnLaravelVersionsBelow('11');
    }

    $domainPath = 'src/Domain';
    $domainRoot = 'Domain';

    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);
    Config::set("ddd.namespaces.{$type}", str($type)->headline()->plural()->toString());

    $expectedFullPath = Path::normalize(base_path($expectedPath));

    if (file_exists($expectedFullPath)) {
        unlink($expectedFullPath);
    }

    expect(file_exists($expectedFullPath))->toBeFalse();

    $command = "ddd:{$type} {$nameInput}";

    Artisan::call($command);

    $output = Artisan::output();

    expect($output)->toContainFilepath($expectedPath);

    expect(file_exists($expectedFullPath))->toBeTrue();

    $contents = file_get_contents($expectedFullPath);

    expect($contents)->toContain("namespace {$expectedNamespace};");
})->with([
    'model' => ['model', 'Other:/MyModels/MyModel', 'Domain\Other\MyModels', 'src/Domain/Other/MyModels/MyModel.php'],
    'model (without overriding)' => ['model', 'Other:MyModels/MyModel', 'Domain\Other\Models\MyModels', 'src/Domain/Other/Models/MyModels/MyModel.php'],
    'exception inside models directory' => ['exception', 'Invoicing:/Models/Exceptions/InvoiceNotFoundException', 'Domain\Invoicing\Models\Exceptions', 'src/Domain/Invoicing/Models/Exceptions/InvoiceNotFoundException.php'],
    'provider' => ['provider', 'Other:/RootLevelProvider', 'Domain\Other', 'src/Domain/Other/RootLevelProvider.php'],
    'policy' => ['policy', 'Other:/RootLevelPolicy', 'Domain\Other', 'src/Domain/Other/RootLevelPolicy.php'],
    'job' => ['job', 'Other:/Custom/Namespaced/Job', 'Domain\Other\Custom\Namespaced', 'src/Domain/Other/Custom/Namespaced/Job.php'],
    'class' => ['class', 'Other:/Models/FakeModel', 'Domain\Other\Models', 'src/Domain/Other/Models/FakeModel.php'],
    'class (subdomain)' => ['class', 'Other.Subdomain:/Models/FakeModel', 'Domain\Other\Subdomain\Models', 'src/Domain/Other/Subdomain/Models/FakeModel.php'],
    'provider (subdomain)' => ['provider', 'Other.Subdomain:/RootLevelProvider', 'Domain\Other\Subdomain', 'src/Domain/Other/Subdomain/RootLevelProvider.php'],
]);
