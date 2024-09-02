<?php

namespace Lunarstorm\LaravelDDD\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\LaravelDDDServiceProvider;
use Lunarstorm\LaravelDDD\Support\DomainCache;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Process\Process;

class TestCase extends Orchestra
{
    public static $configValues = [];

    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->cleanSlate();

            // $this->updateComposer(
            //     set: [
            //         [['autoload', 'psr-4', 'App\\'], 'vendor/orchestra/testbench-core/laravel/app'],
            //         [['autoload', 'psr-4', 'Database\\Factories\\'], 'vendor/orchestra/testbench-core/laravel/database/factories'],
            //         [['autoload', 'psr-4', 'Database\\Seeders\\'], 'vendor/orchestra/testbench-core/laravel/database/seeders'],
            //         [['autoload', 'psr-4', 'Domain\\'], 'vendor/orchestra/testbench-core/laravel/src/Domain'],
            //     ],
            //     forget: [
            //         ['autoload', 'psr-4', 'Domains\\'],
            //         ['autoload', 'psr-4', 'Domain\\'],
            //     ]
            // );

            Factory::guessFactoryNamesUsing(
                fn (string $modelName) => 'Lunarstorm\\LaravelDDD\\Database\\Factories\\'.class_basename($modelName).'Factory'
            );
        });

        $this->beforeApplicationDestroyed(function () {
            $this->cleanSlate();
        });

        parent::setUp();
    }

    public static function configValues(array $values)
    {
        static::$configValues = $values;
    }

    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Repository $config) {
            foreach (static::$configValues as $key => $value) {
                $config->set($key, $value);
            }
        });

        // $this->updateComposer(
        //     set: [
        //         [['autoload', 'psr-4', 'App\\'], 'vendor/orchestra/testbench-core/laravel/app'],
        //     ],
        //     forget: [
        //         ['autoload', 'psr-4', 'Domains\\'],
        //         ['autoload', 'psr-4', 'Domain\\'],
        //     ]
        // );
    }

    protected function getComposerFileContents()
    {
        return file_get_contents(base_path('composer.json'));
    }

    protected function getComposerFileAsArray()
    {
        return json_decode($this->getComposerFileContents(), true);
    }

    protected function updateComposerFileFromArray(array $data)
    {
        file_put_contents(base_path('composer.json'), json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $this;
    }

    protected function updateComposer($set = [], $forget = [])
    {
        $data = $this->getComposerFileAsArray();

        foreach ($forget as $key) {
            Arr::forget($data, $key);
        }

        foreach ($set as $pair) {
            [$key, $value] = $pair;
            data_set($data, $key, $value);
        }

        $this->updateComposerFileFromArray($data);

        return $this;
    }

    protected function forgetComposerValues($keys)
    {
        $composerFile = base_path('composer.json');
        $data = json_decode(file_get_contents($composerFile), true);

        foreach ($keys as $key) {
            Arr::forget($data, $key);
        }

        file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $this;
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelDDDServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }

    protected function composerReload()
    {
        $command = ['composer', 'dump-autoload'];

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {});
    }

    protected function cleanSlate()
    {
        File::copy(__DIR__.'/.skeleton/composer.json', base_path('composer.json'));

        File::delete(base_path('config/ddd.php'));

        File::cleanDirectory(app_path());
        File::cleanDirectory(base_path('database/factories'));

        File::deleteDirectory(resource_path('stubs/ddd'));
        File::deleteDirectory(base_path('Custom'));
        File::deleteDirectory(base_path('src/Domain'));
        File::deleteDirectory(base_path('src/Domains'));
        File::deleteDirectory(app_path('Models'));

        DomainCache::clear();
    }

    protected function setupTestApplication()
    {
        File::copyDirectory(__DIR__.'/.skeleton/app', app_path());
        File::copyDirectory(__DIR__.'/.skeleton/database', base_path('database'));
        File::copyDirectory(__DIR__.'/.skeleton/src/Domain', base_path('src/Domain'));
        File::ensureDirectoryExists(app_path('Models'));

        $this->setDomainPathInComposer('Domain', 'src/Domain');
    }

    protected function setDomainPathInComposer($domainNamespace, $domainPath)
    {
        $this->updateComposer(
            set: [
                [['autoload', 'psr-4', $domainNamespace.'\\'], $domainPath],
            ],
        );

        return $this;
    }
}
