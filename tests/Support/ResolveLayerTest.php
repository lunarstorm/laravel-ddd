<?php

use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\Support\Path;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');

    $this->setupTestApplication();
});

it('can resolve domains to custom layers', function ($domainName, $namespace, $path) {
    Config::set('ddd.layers', [
        'Support' => 'src/Support',
    ]);

    $layer = DomainResolver::resolveLayer($domainName);

    expect($layer)
        ->namespace->toBe($namespace)
        ->path->toBe(Path::normalize($path));
})->with([
    ['Support', 'Support', 'src/Support'],
    ['Invoicing', 'Domain\\Invoicing', 'src/Domain/Invoicing'],
    ['Reporting\\Internal', 'Domain\\Reporting\\Internal', 'src/Domain/Reporting/Internal'],
]);

it('resolves normally when no custom layer is found', function ($domainName, $namespace, $path) {
    Config::set('ddd.layers', [
        'SupportNotMatching' => 'src/Support',
    ]);

    $layer = DomainResolver::resolveLayer($domainName);

    expect($layer)
        ->namespace->toBe($namespace)
        ->path->toBe(Path::normalize($path));
})->with([
    ['Support', 'Domain\\Support', 'src/Domain/Support'],
    ['Invoicing', 'Domain\\Invoicing', 'src/Domain/Invoicing'],
    ['Reporting\\Internal', 'Domain\\Reporting\\Internal', 'src/Domain/Reporting/Internal'],
]);
