<?php

use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

it('can customize the domain path via ddd.domain_path', function () {
    $path = fake()->word();

    Config::set('ddd.domain_path', $path);

    expect(DomainResolver::getConfiguredDomainPath())->toEqual($path);
});

it('can customize the domain root namespace via ddd.domain_namespace', function () {
    Config::set('ddd.domain_namespace', 'Doughmain');

    expect(DomainResolver::getConfiguredDomainNamespace())->toEqual('Doughmain');
});
