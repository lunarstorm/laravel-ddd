<?php

use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\Facades\DDD;

beforeEach(function () {
    $this->cleanSlate();

    $this->latestConfig = require DDD::packagePath('config/ddd.php');
});

afterEach(function () {
    $this->cleanSlate();
});

it('can update and merge current config file with latest copy from package', function () {
    $path = __DIR__.'/resources/config.sparse.php';

    File::copy($path, config_path('ddd.php'));

    expect(file_exists($path))->toBeTrue();

    $originalContents = file_get_contents($path);

    expect(file_get_contents(config_path('ddd.php')))->toEqual($originalContents);

    $original = include $path;

    $config = DDD::config();

    $config->syncWithLatest()->save();

    $updatedContents = file_get_contents(config_path('ddd.php'));

    expect($updatedContents)->not->toEqual($originalContents);

    $updatedConfig = include config_path('ddd.php');

    // Expect original values to be retained
    foreach ($original as $key => $value) {
        if (is_array($value)) {
            // We won't worry about arrays for now
            continue;
        }

        expect($updatedConfig[$key])->toEqual($value);
    }

    // Expect the updated config to have all top-level keys from the latest config
    expect($updatedConfig)->toHaveKeys(array_keys($this->latestConfig));
});
