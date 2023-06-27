<?php

use function PHPUnit\Framework\assertMatchesRegularExpression;

expect()->extend('toMatchRegularExpression', function ($pattern, string $message = '') {
    assertMatchesRegularExpression($pattern, $this->value, $message);

    return $this;
});
