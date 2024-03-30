<?php

use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\Path;

it('can initialize a domain', function ($domainName, $subdomainName) {
    $domain = new Domain($domainName, $subdomainName);

    expect($domain)
        ->domain->toBe($domainName)
        ->subdomain->toBe($subdomainName);
})->with('domainSubdomain');

it('can extract domain and subdomain from dot or slash separated string', function ($domainName, $subdomainName, $separator) {
    $dotPath = $subdomainName ? "{$domainName}{$separator}{$subdomainName}" : $domainName;
    $domain = new Domain($dotPath);

    expect($domain)
        ->domain->toBe($domainName)
        ->subdomain->toBe($subdomainName);
})->with('domainSubdomain')->with(['.', '\\', '/']);

it('can describe a domain model', function ($domainName, $name, $expectedFQN, $expectedPath) {
    expect((new Domain($domainName))->model($name))
        ->name->toBe($name)
        ->fullyQualifiedName->toBe($expectedFQN)
        ->path->toBe(Path::normalize($expectedPath));
})->with([
    ['Reporting', 'InvoiceReport', 'Domain\\Reporting\\Models\\InvoiceReport', 'src/Domain/Reporting/Models/InvoiceReport.php'],
    ['Reporting.Internal', 'InvoiceReport', 'Domain\\Reporting\\Internal\\Models\\InvoiceReport', 'src/Domain/Reporting/Internal/Models/InvoiceReport.php'],
]);

it('can describe a domain factory', function ($domainName, $name, $expectedFQN, $expectedPath) {
    expect((new Domain($domainName))->factory($name))
        ->name->toBe($name)
        ->fullyQualifiedName->toBe($expectedFQN)
        ->path->toBe(Path::normalize($expectedPath));
})->with([
    ['Reporting', 'InvoiceReportFactory', 'Database\\Factories\\Reporting\\InvoiceReportFactory', 'database/factories/Reporting/InvoiceReportFactory.php'],
    ['Reporting.Internal', 'InvoiceReportFactory', 'Database\\Factories\\Reporting\\Internal\\InvoiceReportFactory', 'database/factories/Reporting/Internal/InvoiceReportFactory.php'],
]);

it('can describe a data transfer object', function ($domainName, $name, $expectedFQN, $expectedPath) {
    expect((new Domain($domainName))->dataTransferObject($name))
        ->name->toBe($name)
        ->fullyQualifiedName->toBe($expectedFQN)
        ->path->toBe(Path::normalize($expectedPath));
})->with([
    ['Reporting', 'InvoiceData', 'Domain\\Reporting\\Data\\InvoiceData', 'src/Domain/Reporting/Data/InvoiceData.php'],
    ['Reporting.Internal', 'InvoiceData', 'Domain\\Reporting\\Internal\\Data\\InvoiceData', 'src/Domain/Reporting/Internal/Data/InvoiceData.php'],
]);

it('can describe a view model', function ($domainName, $name, $expectedFQN, $expectedPath) {
    expect((new Domain($domainName))->viewModel($name))
        ->name->toBe($name)
        ->fullyQualifiedName->toBe($expectedFQN)
        ->path->toBe(Path::normalize($expectedPath));
})->with([
    ['Reporting', 'InvoiceReportViewModel', 'Domain\\Reporting\\ViewModels\\InvoiceReportViewModel', 'src/Domain/Reporting/ViewModels/InvoiceReportViewModel.php'],
    ['Reporting.Internal', 'InvoiceReportViewModel', 'Domain\\Reporting\\Internal\\ViewModels\\InvoiceReportViewModel', 'src/Domain/Reporting/Internal/ViewModels/InvoiceReportViewModel.php'],
]);

it('can describe a value object', function ($domainName, $name, $expectedFQN, $expectedPath) {
    expect((new Domain($domainName))->valueObject($name))
        ->name->toBe($name)
        ->fullyQualifiedName->toBe($expectedFQN)
        ->path->toBe(Path::normalize($expectedPath));
})->with([
    ['Reporting', 'InvoiceTotal', 'Domain\\Reporting\\ValueObjects\\InvoiceTotal', 'src/Domain/Reporting/ValueObjects/InvoiceTotal.php'],
    ['Reporting.Internal', 'InvoiceTotal', 'Domain\\Reporting\\Internal\\ValueObjects\\InvoiceTotal', 'src/Domain/Reporting/Internal/ValueObjects/InvoiceTotal.php'],
]);

it('can describe an action', function ($domainName, $name, $expectedFQN, $expectedPath) {
    expect((new Domain($domainName))->action($name))
        ->name->toBe($name)
        ->fullyQualifiedName->toBe($expectedFQN)
        ->path->toBe(Path::normalize($expectedPath));
})->with([
    ['Reporting', 'SendInvoiceReport', 'Domain\\Reporting\\Actions\\SendInvoiceReport', 'src/Domain/Reporting/Actions/SendInvoiceReport.php'],
    ['Reporting.Internal', 'SendInvoiceReport', 'Domain\\Reporting\\Internal\\Actions\\SendInvoiceReport', 'src/Domain/Reporting/Internal/Actions/SendInvoiceReport.php'],
]);

it('can describe an anonymous domain object', function ($domainName, $objectType, $objectName, $expectedFQN, $expectedPath) {
    expect((new Domain($domainName))->object($objectType, $objectName))
        ->name->toBe($objectName)
        ->fullyQualifiedName->toBe($expectedFQN)
        ->path->toBe(Path::normalize($expectedPath));
})->with([
    ['Invoicing', 'rule', 'SomeRule', 'Domain\\Invoicing\\Rules\\SomeRule', 'src/Domain/Invoicing/Rules/SomeRule.php'],
    ['Other', 'thing', 'Something', 'Domain\\Other\\Things\\Something', 'src/Domain/Other/Things/Something.php'],
]);
