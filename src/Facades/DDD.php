<?php

namespace Tey\LaravelDDD\Facades;

use Illuminate\Support\Facades\Facade;
use Tey\LaravelDDD\DomainManager;

/**
 * @see DomainManager
 *
 * @method static void filterAutoloadPathsUsing(callable $filter)
 * @method static ?callable getAutoloadFilter()
 * @method static void resolveObjectSchemaUsing(callable $resolver)
 * @method static string packagePath(string $path = '')
 * @method static \Tey\LaravelDDD\Support\AutoloadManager autoloader()
 * @method static \Tey\LaravelDDD\ConfigManager config()
 * @method static \Tey\LaravelDDD\StubManager stubs()
 * @method static \Tey\LaravelDDD\ComposerManager composer()
 */
class DDD extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DomainManager::class;
    }
}
