<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('can publish config file', function () {
    $expectedPath = base_path('config/ddd.php');

    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }

    expect(file_exists($expectedPath))->toBeFalse();

    Artisan::call('vendor:publish', [
        '--tag' => 'ddd-config',
    ]);

    expect(file_exists($expectedPath))->toBeTrue();
});

it('can publish stubs', function () {
    $dir = resource_path('stubs/ddd');

    if (File::exists($dir)) {
        File::deleteDirectory($dir);
    }

    expect(File::exists($dir))->toBeFalse();

    Artisan::call('vendor:publish', [
        '--tag' => 'ddd-stubs',
    ]);

    expect(File::exists($dir))->toBeTrue();
    expect(File::isEmptyDirectory($dir))->toBeFalse();
});
