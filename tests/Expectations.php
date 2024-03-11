<?php

use function PHPUnit\Framework\assertMatchesRegularExpression;

expect()->extend('toMatchRegularExpression', function ($pattern, string $message = '') {
    assertMatchesRegularExpression($pattern, $this->value, $message);

    return $this;
});

expect()->extend('toContainFilepath', function ($path) {
    $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

    return $this->toContain($normalizedPath);
});
