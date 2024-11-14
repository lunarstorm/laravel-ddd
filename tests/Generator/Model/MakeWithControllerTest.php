<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Models\DomainModel;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Tests\Fixtures\Enums\Feature;

beforeEach(function () {
    config([
        'ddd.domain_path' => 'src/Domain',
        'ddd.domain_namespace' => 'Domain',
        'ddd.base_model' => DomainModel::class,
        'ddd.application_namespace' => 'App\Modules',
        'ddd.application_path' => 'app/Modules',
        'ddd.application_objects' => [
            'controller',
        ],
    ]);

    $this->setupTestApplication();
});

it('can generate domain model with controller', function ($domainName, $modelName, $controllerName, $generatedPaths) {
    $domain = new Domain($domainName);

    foreach ($generatedPaths as $path) {
        $path = base_path($path);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    $command = [
        'ddd:model',
        [
            'name' => $modelName,
            '--domain' => $domain->dotName,
            '--controller' => true,
        ],
    ];

    Artisan::call(...$command);

    $output = Artisan::output();

    foreach ($generatedPaths as $path) {
        if (Feature::IncludeFilepathInGeneratorCommandOutput->exists()) {
            expect($output)->toContainFilepath($path);
        }

        expect(file_exists(base_path($path)))->toBeTrue("Expecting {$path} to exist");
    }
})->with([
    'Invoicing:Record' => [
        'Invoicing',
        'Record',
        'RecordController',
        [
            'src/Domain/Invoicing/Models/Record.php',
            'app/Modules/Invoicing/Controllers/RecordController.php',
        ],
    ],

    'Invoicing:RecordEntry' => [
        'Invoicing',
        'RecordEntry',
        'RecordEntryController',
        [
            'src/Domain/Invoicing/Models/RecordEntry.php',
            'app/Modules/Invoicing/Controllers/RecordEntryController.php',
        ],
    ],

    'Reporting.Internal:ReportSubmission' => [
        'Reporting.Internal',
        'ReportSubmission',
        'ReportSubmissionController',
        [
            'src/Domain/Reporting/Internal/Models/ReportSubmission.php',
            'app/Modules/Reporting/Internal/Controllers/ReportSubmissionController.php',
        ],
    ],
]);
