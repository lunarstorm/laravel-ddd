<?php

namespace Lunarstorm\LaravelDDD\Tests;

use Illuminate\Contracts\Config\Repository;

trait AutoloadingTest
{
    // public static $configValues = [];

    // protected function defineEnvironment($app)
    // {
    //     static::$configValues = [
    //         'ddd.domain_path' => 'src/Domain',
    //         'ddd.domain_namespace' => 'Domain',
    //         'ddd.application_namespace' => 'Application',
    //         'ddd.application_path' => 'src/Application',
    //         'ddd.application_objects' => [
    //             'controller',
    //             'request',
    //             'middleware',
    //         ],
    //         'ddd.layers' => [
    //             'Infrastructure' => 'src/Infrastructure',
    //         ],
    //         'ddd.autoload_ignore' => [
    //             'Tests',
    //             'Database/Migrations',
    //         ],
    //         'cache.default' => 'file',
    //         ...static::$configValues,
    //     ];

    //     tap($app['config'], function (Repository $config) {
    //         foreach (static::$configValues as $key => $value) {
    //             $config->set($key, $value);
    //         }
    //     });
    // }
}
