<?php

namespace Lunarstorm\LaravelDDD\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\LaravelDDDServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Process\Process;

class TestCase extends Orchestra
{
    public static $configValues = [];

    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->cleanSlate();

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

        return $this;
    }

    protected function cleanSlate()
    {
        File::delete(base_path('config/ddd.php'));

        // File::cleanDirectory(app_path());
        File::cleanDirectory(app_path('Models'));
        File::cleanDirectory(base_path('database/factories'));
        File::cleanDirectory(base_path('src'));

        File::deleteDirectory(resource_path('stubs/ddd'));
        File::deleteDirectory(base_path('stubs'));
        File::deleteDirectory(base_path('Custom'));
        File::deleteDirectory(app_path('Policies'));
        File::deleteDirectory(app_path('Modules'));
        File::deleteDirectory(base_path('bootstrap/cache/ddd'));

        File::copy(__DIR__.'/.skeleton/composer.json', base_path('composer.json'));

        return $this;
    }

    protected function cleanStubs()
    {
        File::cleanDirectory(base_path('stubs'));

        return $this;
    }

    protected function setupTestApplication()
    {
        $this->cleanSlate();

        File::ensureDirectoryExists(app_path());
        File::ensureDirectoryExists(app_path('Models'));

        $skeletonAppFolders = glob(__DIR__.'/.skeleton/app/*', GLOB_ONLYDIR);

        foreach ($skeletonAppFolders as $folder) {
            File::copyDirectory($folder, app_path(basename($folder)));
        }

        File::copyDirectory(__DIR__.'/.skeleton/database', base_path('database'));
        File::copyDirectory(__DIR__.'/.skeleton/src', base_path('src'));
        File::copy(__DIR__.'/.skeleton/bootstrap/providers.php', base_path('bootstrap/providers.php'));

        $this->setAutoloadPathInComposer('Domain', 'src/Domain');
        $this->setAutoloadPathInComposer('Application', 'src/Application');
        $this->setAutoloadPathInComposer('Infrastructure', 'src/Infrastructure');

        return $this;
    }

    protected function setAutoloadPathInComposer($namespace, $path, bool $reload = true)
    {
        $this->updateComposer(
            set: [
                [['autoload', 'psr-4', $namespace.'\\'], $path],
            ],
        );

        if ($reload) {
            $this->composerReload();
        }

        return $this;
    }
}
