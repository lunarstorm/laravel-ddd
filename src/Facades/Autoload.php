<?php

namespace Lunarstorm\LaravelDDD\Facades;

use Illuminate\Support\Facades\Facade;
use Lunarstorm\LaravelDDD\Support\AutoloadManager;

/**
 * @see AutoloadManager
 *
 * @method static void boot()
 * @method static void run()
 * @method static array getAllLayerPaths()
 * @method static array getCustomLayerPaths()
 * @method static array getRegisteredCommands()
 * @method static array getRegisteredProviders()
 * @method static Autoload cacheCommands()
 * @method static Autoload cacheProviders()
 */
class Autoload extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AutoloadManager::class;
    }
}
