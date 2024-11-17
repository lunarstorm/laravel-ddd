<?php

use Illuminate\Support\Facades\Artisan;
use Lunarstorm\LaravelDDD\Support\Path;

use function PHPUnit\Framework\assertMatchesRegularExpression;

expect()->extend('toMatchRegularExpression', function ($pattern, string $message = '') {
    assertMatchesRegularExpression($pattern, $this->value, $message);

    return $this;
});

expect()->extend('toContainFilepath', function ($path) {
    return $this->toContain(Path::normalize($path));
});

expect()->extend('toEqualPath', function ($path) {
    return $this->toEqual(Path::normalize($path));
});

expect()->extend('toGenerateFileWithNamespace', function ($expectedPath, $expectedNamespace) {
    $command = $this->value;

    $expectedFullPath = Path::normalize(base_path($expectedPath));

    if (file_exists($expectedFullPath)) {
        unlink($expectedFullPath);
    }

    Artisan::call($command);

    $output = Artisan::output();

    expect($output)->toContainFilepath($expectedPath);

    expect(file_exists($expectedFullPath))->toBeTrue();

    $contents = file_get_contents($expectedFullPath);

    expect($contents)->toContain("namespace {$expectedNamespace};");

    return $this;
});
