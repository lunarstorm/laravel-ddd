<?php

namespace Lunarstorm\LaravelDDD\Facades;

use Illuminate\Support\Facades\Facade;
use Lunarstorm\LaravelDDD\DomainManager;

/**
 * @see DomainManager
 *
 * @method static void filterAutoloadPathsUsing(callable $filter)
 * @method static ?callable getAutoloadFilter()
 * @method static void resolveObjectSchemaUsing(callable $resolver)
 * @method static string packagePath(string $path = '')
 * @method static \Lunarstorm\LaravelDDD\Support\AutoloadManager autoloader()
 * @method static \Lunarstorm\LaravelDDD\ConfigManager config()
 * @method static \Lunarstorm\LaravelDDD\StubManager stubs()
 * @method static \Lunarstorm\LaravelDDD\ComposerManager composer()
 */
class DDD extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DomainManager::class;
    }
}
