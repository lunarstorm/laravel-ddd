<?php

namespace Lunarstorm\LaravelDDD\Facades;

use Illuminate\Support\Facades\Facade;
use Lunarstorm\LaravelDDD\StubManager;

/**
 * @see \Lunarstorm\LaravelDDD\DomainManager
 *
 * @method static void filterAutoloadPathsUsing(callable $filter)
 * @method static void resolveNamespaceUsing(callable $resolver)
 * @method static string packagePath(string $path = '')
 * @method static StubManager stubs()
 */
class DDD extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Lunarstorm\LaravelDDD\DomainManager::class;
    }
}
