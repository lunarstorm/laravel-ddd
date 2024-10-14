<?php

use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Facades\DDD;

beforeEach(function () {
    Config::set('ddd.domain_path', 'src/Domain');
    Config::set('ddd.domain_namespace', 'Domain');
    Config::set('ddd.application_layer', [
        'path' => 'app/Modules',
        'namespace' => 'App\Modules',
    ]);

    $this->setupTestApplication();
})->markTestIncomplete('wip');

it('can register a custom application layer namespace resolver', function () {
    DDD::resolveApplicationLayerNamespaceUsing(function (string $domain, string $type, ?string $object) {
        // src/App/Api/Controllers/<domainName>/<modelName>ApiController.php
        return match ($type) {
            // 'command' => 'Api\\' . Str::plural($domain) . '\Commands',
            // 'provider' => 'App\Modules\\' . Str::plural($domain) . '\Providers',
            // 'model' => 'App\Modules\\' . Str::plural($domain) . '\Models',
            default => null,
        };
    });
});
