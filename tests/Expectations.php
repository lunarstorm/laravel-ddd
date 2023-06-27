<?php

expect()->extend('ifElse', function ($condition, callable $callbackWhenTrue, callable $callbackElse) {
    return $this
        ->when($condition, $callbackWhenTrue)
        ->unless($condition, $callbackElse);
});
