<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainModelMakeCommand extends ModelMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:model';

    protected function createFactory()
    {
        $factory = Str::studly($this->argument('name'));

        $this->call(DomainFactoryMakeCommand::class, [
            'name' => $factory.'Factory',
            '--domain' => $this->domain->dotName,
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    // protected function createMigration()
    // {
    //     $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

    //     if ($this->option('pivot')) {
    //         $table = Str::singular($table);
    //     }

    //     $this->call('make:migration', [
    //         'name' => "create_{$table}_table",
    //         '--create' => $table,
    //     ]);
    // }

    /**
     * Create a seeder file for the model.
     *
     * @return void
     */
    protected function createSeeder()
    {
        $seeder = Str::studly(class_basename($this->argument('name')));

        $this->call(DomainSeederMakeCommand::class, [
            'name' => "{$seeder}Seeder",
            '--domain' => $this->domain->dotName,
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = $this->qualifyClass($this->getNameInput());

        $this->call(DomainControllerMakeCommand::class, array_filter([
            'name' => "{$controller}Controller",
            '--model' => $this->option('resource') || $this->option('api') ? $modelName : null,
            '--api' => $this->option('api'),
            '--requests' => $this->option('requests') || $this->option('all'),
            '--test' => $this->option('test'),
            '--pest' => $this->option('pest'),
        ]));
    }

    /**
     * Create a policy file for the model.
     *
     * @return void
     */
    protected function createPolicy()
    {
        $policy = Str::studly(class_basename($this->argument('name')));

        $this->call(DomainPolicyMakeCommand::class, [
            'name' => "{$policy}Policy",
            '--domain' => $this->domain->dotName,
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }
}
