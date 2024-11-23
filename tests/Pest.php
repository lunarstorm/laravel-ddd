<?php

use Lunarstorm\LaravelDDD\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function skipOnLaravelVersionsBelow($minimumVersion)
{
    $version = app()->version();

    if (version_compare($version, $minimumVersion, '<')) {
        test()->markTestSkipped("Only available on Laravel {$minimumVersion}+ (Current version: {$version}).");
    }
}

function onlyOnLaravelVersionsBelow($minimumVersion)
{
    $version = app()->version();

    if (! version_compare($version, $minimumVersion, '<')) {
        test()->markTestSkipped("Does not apply to Laravel {$minimumVersion}+ (Current version: {$version}).");
    }
}

function setConfigValues(array $values)
{
    TestCase::configValues($values);
}
