<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('can upgrade 0.x config to 1.x', function (string $pathToOldConfig, array $expectedValues) {
    $path = config_path('ddd.php');

    File::copy($pathToOldConfig, $path);

    expect(file_exists($path))->toBeTrue();

    $this->artisan('ddd:upgrade')
        ->expectsOutputToContain('Configuration upgraded successfully.')
        ->execute();

    Artisan::call('config:clear');

    $expectedValues = Arr::dot($expectedValues);

    $configAsArray = require config_path('ddd.php');

    foreach ($expectedValues as $path => $value) {
        expect(data_get($configAsArray, $path))
            ->toEqual($value, "Config {$path} does not match expected value.");
    }
})->with('configUpgrades');

it('skips upgrade if config file was not published', function () {
    $path = config_path('ddd.php');

    if (file_exists($path)) {
        unlink($path);
    }

    expect(file_exists($path))->toBeFalse();

    $this->artisan('ddd:upgrade')
        ->expectsOutputToContain('Config file was not published. Nothing to upgrade!')
        ->execute();

    expect(file_exists($path))->toBeFalse();
});
