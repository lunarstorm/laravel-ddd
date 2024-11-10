<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

beforeEach(function () {
    $this->cleanSlate();
    $this->setupTestApplication();

    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    Config::set('ddd.application', [
        'path' => 'app/Modules',
        'namespace' => 'App\Modules',
        'objects' => ['controller', 'request'],
    ]);
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
        fn ($output) => $output->toContainFilepath($relativePath),
    );

    expect(file_exists($expectedPath))->toBeTrue();

    expect($contents = file_get_contents($expectedPath))
        ->toContain("namespace {$expectedNamespace};");

    if (Feature::Laravel11->exists()) {
        // These assertions don't seem to pass on Laravel 10
        expect($contents)
            ->toContain("use App\Http\Controllers\Controller;")
            ->toContain('extends Controller');
    }
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

    Artisan::call('ddd:controller', [
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

    Artisan::call('ddd:controller', [
        'name' => $controllerName,
        '--domain' => $domainName,
        '--model' => $modelName,
        '--requests' => true,
    ]);

    $output = Artisan::output();

    foreach ($generatedPaths as $path) {
        if (Feature::IncludeFilepathInGeneratorCommandOutput->exists()) {
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

it('does not extend base controller if base controller not found', function ($domainName, $controllerName, $relativePath, $expectedNamespace) {
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    // Remove the base controller
    $baseControllerPath = base_path('app/Http/Controllers/Controller.php');

    if (file_exists($baseControllerPath)) {
        unlink($baseControllerPath);
    }

    expect(file_exists($baseControllerPath))->toBeFalse();

    Artisan::call("ddd:controller {$domainName}:{$controllerName}");

    expect(file_exists($expectedPath))->toBeTrue();

    expect($contents = file_get_contents($expectedPath))
        ->toContain("namespace {$expectedNamespace};");

    if (Feature::Laravel11->exists()) {
        // These assertions don't seem to pass on Laravel 10
        expect($contents)
            ->not->toContain("use App\Http\Controllers\Controller;")
            ->not->toContain('extends Controller');
    }
})->with([
    'Invoicing:InvoiceController' => [
        'Invoicing',
        'InvoiceController',
        'app/Modules/Invoicing/Controllers/InvoiceController.php',
        'App\Modules\Invoicing\Controllers',
    ],
]);

it('does not attempt to extend base controller when using custom stubs', function ($domainName, $controllerName, $relativePath, $expectedNamespace, $stubFolder) {
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    $baseControllerPath = app()->basePath('app/Http/Controllers/Controller.php');

    expect(file_exists($baseControllerPath))->toBeTrue();

    // Publish a custom controller.stub
    $customStub = <<<'STUB'
<?php

namespace {{ namespace }};

class {{ class }}
{
    use CustomControllerTrait;
}
STUB;

    File::ensureDirectoryExists(app()->basePath($stubFolder));
    file_put_contents(app()->basePath($stubFolder.'/controller.plain.stub'), $customStub);
    expect(file_exists(app()->basePath($stubFolder.'/controller.plain.stub')))->toBeTrue();

    Artisan::call("ddd:controller {$domainName}:{$controllerName}");

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))
        ->toContain("namespace {$expectedNamespace};")
        ->toContain('use CustomControllerTrait;')
        ->not->toContain("use App\Http\Controllers\Controller;")
        ->not->toContain('extends Controller');

    $this->cleanStubs();
})->with([
    'Invoicing:InvoiceController' => [
        'Invoicing',
        'InvoiceController',
        'app/Modules/Invoicing/Controllers/InvoiceController.php',
        'App\Modules\Invoicing\Controllers',
    ],
])->with([
    'stubs',
    'stubs/ddd',
]);
