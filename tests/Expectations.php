<?php

use Lunarstorm\LaravelDDD\Support\Path;

use function PHPUnit\Framework\assertMatchesRegularExpression;

expect()->extend('toMatchRegularExpression', function ($pattern, string $message = '') {
    assertMatchesRegularExpression($pattern, $this->value, $message);

    return $this;
});

expect()->extend('toContainFilepath', function ($path) {
    return $this->toContain(Path::normalize($path));
});
