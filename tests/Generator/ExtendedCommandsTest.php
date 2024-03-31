<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Lunarstorm\LaravelDDD\Support\Domain;

it('can generate extended objects', function ($type, $objectName, $domainPath, $domainRoot) {
    if (in_array($type, ['enum'])) {
        skipOnLaravelVersionsBelow('11');
    }

    Config::set('ddd.domain_path', $domainPath);
    Config::set('ddd.domain_namespace', $domainRoot);

    $domain = new Domain('Other');
    $domainObject = $domain->object($type, $objectName);

    $relativePath = $domainObject->path;
    $expectedNamespace = $domainObject->namespace;
    $expectedPath = base_path($relativePath);

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    $command = "ddd:{$type} {$domain->domain}:{$objectName}";

    Artisan::call($command);

    expect(Artisan::output())->toContainFilepath($relativePath);

    expect(file_exists($expectedPath))->toBeTrue();

    expect(file_get_contents($expectedPath))->toContain("namespace {$expectedNamespace};");
})->with([
    'cast' => ['cast', 'SomeCast'],
    'channel' => ['channel', 'SomeChannel'],
    'command' => ['command', 'SomeCommand'],
    'enum' => ['enum', 'SomeEnum'],
    'event' => ['event', 'SomeEvent'],
    'exception' => ['exception', 'SomeException'],
    'job' => ['job', 'SomeJob'],
    'listener' => ['listener', 'SomeListener'],
    'mail' => ['mail', 'SomeMail'],
    'notification' => ['notification', 'SomeNotification'],
    'observer' => ['observer', 'SomeObserver'],
    'policy' => ['policy', 'SomePolicy'],
    'provider' => ['provider', 'SomeProvider'],
    'resource' => ['resource', 'SomeResource'],
    'rule' => ['rule', 'SomeRule'],
    'scope' => ['scope', 'SomeScope'],
])->with('domainPaths');
