<?php

use Lunarstorm\LaravelDDD\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function skipOnLaravelVersionsBelow($minimumVersion)
{
    $version = app()->version();

    if (version_compare($version, $minimumVersion, '<')) {
        test()->markTestSkipped("Only relevant from Laravel {$minimumVersion} onwards (Current version: {$version}).");
    }
}

function ifSupportsPromptForMissingInput()
{
    return skipOnLaravelVersionsBelow('9.49.0');
}
