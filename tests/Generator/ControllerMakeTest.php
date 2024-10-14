<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Laravel\Prompts\Prompt;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;
use Mockery\Mock;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    Config::set('ddd.application_layer', [
        'path' => 'app/Modules',
        'namespace' => 'App\Modules',
        'objects' => ['controller', 'request'],
    ]);

    $this->setupTestApplication();
});

it('can generate domain controller', function ($domainName, $controllerName, $relativePath, $expectedNamespace) {
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:controller {$domainName}:{$controllerName}");

    expect($output = Artisan::output())->when(
        Feature::IncludeFilepathInGeneratorCommandOutput->exists(),
        fn($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))
        ->toContain("namespace {$expectedNamespace};");
})->with([
    'Invoicing:InvoiceController' => [
        'Invoicing',
        'InvoiceController',
        'app/Modules/Invoicing/Controllers/InvoiceController.php',
        'App\Modules\Invoicing\Controllers',
    ],

    'Reporting.Internal:ReportSubmissionController' => [
        'Reporting.Internal',
        'ReportSubmissionController',
        'app/Modules/Reporting/Internal/Controllers/ReportSubmissionController.php',
        'App\Modules\Reporting\Internal\Controllers',
    ],
]);

it('can generate domain resource controller from model', function ($domainName, $controllerName, $relativePath, $modelName, $modelClass) {
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call("ddd:controller",[
        'name' => $controllerName,
        '--domain' => $domainName,
        '--model' => $modelName,
    ]);

    expect(file_exists($expectedPath))->toBeTrue();

    $modelVariable = lcfirst(class_basename($modelClass));

    expect(file_get_contents($expectedPath))
        ->toContain("use {$modelClass};")
        ->toContain("{$modelName} \${$modelVariable})");
})->with([
    'Invoicing:InvoiceController --model=Invoice' => [
        'Invoicing',
        'InvoiceController',
        'app/Modules/Invoicing/Controllers/InvoiceController.php',
        'Invoice',
        'Domain\Invoicing\Models\Invoice',
    ],

    'Invoicing:Payment/InvoicePaymentController --model=InvoicePayment' => [
        'Invoicing',
        'Payment/InvoicePaymentController',
        'app/Modules/Invoicing/Controllers/Payment/InvoicePaymentController.php',
        'InvoicePayment',
        'Domain\Invoicing\Models\InvoicePayment',
    ],

    'Reporting.Internal:Archived/ReportArchiveController --model=ReportArchive' => [
        'Reporting.Internal',
        'Archived/ReportArchiveController',
        'app/Modules/Reporting/Internal/Controllers/Archived/ReportArchiveController.php',
        'ReportArchive',
        'Domain\Reporting\Internal\Models\ReportArchive',
    ],
]);

it('can generate domain controller with requests', function ($domainName, $controllerName, $controllerPath, $modelName, $modelClass, $generatedPaths) {
    $generatedPaths = [
        $controllerPath,
        ...$generatedPaths,
    ];

    foreach ($generatedPaths as $path) {
        $path = base_path($path);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    Artisan::call("ddd:controller", [
        'name' => $controllerName,
        '--domain' => $domainName,
        '--model' => $modelName,
        '--requests' => true,
    ]);

    $output = Artisan::output();

    foreach ($generatedPaths as $path) {
        if(Feature::IncludeFilepathInGeneratorCommandOutput->exists()){
            expect($output)->toContainFilepath($path);
        }

        expect(file_exists(base_path($path)))->toBeTrue("Expecting {$path} to exist");
    }

    $modelVariable = lcfirst(class_basename($modelClass));

    expect(file_get_contents(base_path($controllerPath)))
        ->toContain("use {$modelClass};")
        ->toContain("store(Store{$modelName}Request \$request)")
        ->toContain("update(Update{$modelName}Request \$request, {$modelName} \${$modelVariable})");
})->with([
    'Invoicing:InvoiceController --model=Invoice' => [
        'Invoicing',
        'InvoiceController',
        'app/Modules/Invoicing/Controllers/InvoiceController.php',
        'Invoice',
        'Domain\Invoicing\Models\Invoice',
        [
            'app/Modules/Invoicing/Requests/StoreInvoiceRequest.php',
            'app/Modules/Invoicing/Requests/UpdateInvoiceRequest.php',
        ],
    ],

    'Invoicing:Payment/InvoicePaymentController --model=InvoicePayment' => [
        'Invoicing',
        'Payment/InvoicePaymentController',
        'app/Modules/Invoicing/Controllers/Payment/InvoicePaymentController.php',
        'InvoicePayment',
        'Domain\Invoicing\Models\InvoicePayment',
        [
            'app/Modules/Invoicing/Requests/Payment/StoreInvoicePaymentRequest.php',
            'app/Modules/Invoicing/Requests/Payment/UpdateInvoicePaymentRequest.php',
        ],
    ],

    'Reporting.Internal:Archived/ReportArchiveController --model=ReportArchive' => [
        'Reporting.Internal',
        'Archived/ReportArchiveController',
        'app/Modules/Reporting/Internal/Controllers/Archived/ReportArchiveController.php',
        'ReportArchive',
        'Domain\Reporting\Internal\Models\ReportArchive',
        [
            'app/Modules/Reporting/Internal/Requests/Archived/StoreReportArchiveRequest.php',
            'app/Modules/Reporting/Internal/Requests/Archived/UpdateReportArchiveRequest.php',
        ],
    ],
]);
