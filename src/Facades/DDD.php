<?php

namespace Lunarstorm\LaravelDDD\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lunarstorm\LaravelDDD\DomainManager
 *
 * @method static void filterAutoloadPathsUsing(callable $filter)
 */
class DDD extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Lunarstorm\LaravelDDD\DomainManager::class;
    }
}
