<?php

namespace Lunarstorm\LaravelDDD\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lunarstorm\LaravelDDD\LaravelDDD
 */
class LaravelDDD extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Lunarstorm\LaravelDDD\LaravelDDD::class;
    }
}
