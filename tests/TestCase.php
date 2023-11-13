<?php

namespace Lunarstorm\LaravelDDD\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lunarstorm\LaravelDDD\LaravelDDDServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Process\Process;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanFilesAndFolders();

        $composerFile = base_path('composer.json');
        $data = json_decode(file_get_contents($composerFile), true);

        // Reset the domain namespace
        Arr::forget($data, ['autoload', 'psr-4', 'Domains\\']);
        Arr::forget($data, ['autoload', 'psr-4', 'Domain\\']);

        // Set up the essential app namespaces
        data_set($data, ['autoload', 'psr-4', 'App\\'], 'vendor/orchestra/testbench-core/laravel/app');
        data_set($data, ['autoload', 'psr-4', 'Database\\Factories\\'], 'vendor/orchestra/testbench-core/laravel/database/factories');
        data_set($data, ['autoload', 'psr-4', 'Database\\Seeders\\'], 'vendor/orchestra/testbench-core/laravel/database/seeders');

        file_put_contents($composerFile, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $this->composerReload();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Lunarstorm\\LaravelDDD\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->beforeApplicationDestroyed(function () {
            $this->cleanFilesAndFolders();
        });
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
            ->run(function ($type, $output) {
            });
    }

    protected function cleanFilesAndFolders()
    {
        File::delete(base_path('config/ddd.php'));

        File::cleanDirectory(app_path());
        File::cleanDirectory(base_path('database/factories'));

        File::deleteDirectory(resource_path('stubs/ddd'));
        File::deleteDirectory(base_path('Custom'));
        File::deleteDirectory(base_path('src/Domain'));
        File::deleteDirectory(base_path('src/Domains'));
    }
}
