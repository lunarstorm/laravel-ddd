<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Commands\Migration\DomainMigrateMakeCommand;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

class DomainModelMakeCommand extends ModelMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:model';

    protected function getNameInput()
    {
        return Str::studly($this->argument('name'));
    }

    public function handle()
    {
        $this->beforeHandle();

        $this->createBaseModelIfNeeded();

        parent::handle();

        $this->afterHandle();
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if ($baseModel = $this->getBaseModel()) {
            $baseModelClass = class_basename($baseModel);

            $replacements = [
                'use Illuminate\Database\Eloquent\Model;' => "use {$baseModel};",
                'extends Model' => "extends {$baseModelClass}",
            ];

            $stub = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $stub
            );

            $stub = $this->sortImports($stub);
        }

        return $stub;
    }

    protected function createFactory()
    {
        $factory = Str::studly($this->argument('name'));

        $this->call(DomainFactoryMakeCommand::class, [
            'name' => $factory.'Factory',
            '--domain' => $this->domain->dotName,
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call(DomainMigrateMakeCommand::class, [
            'name' => "create_{$table}_table",
            '--domain' => $this->domain->dotName,
            '--create' => $table,
        ]);
    }

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

        $controllerName = "{$controller}Controller";

        $this->call(DomainControllerMakeCommand::class, array_filter([
            'name' => $controllerName,
            '--domain' => $this->domain->dotName,
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

    protected function createBaseModelIfNeeded()
    {
        if (! $this->shouldCreateBaseModel()) {
            return;
        }

        $baseModel = config('ddd.base_model');

        $this->warn("Base model {$baseModel} doesn't exist, generating...");

        $domain = DomainResolver::guessDomainFromClass($baseModel);

        $name = Str::after($baseModel, $domain);

        $this->call(DomainBaseModelMakeCommand::class, [
            '--domain' => $domain,
            'name' => $name,
        ]);
    }

    protected function getBaseModel(): ?string
    {
        return config('ddd.base_model', null);
    }

    protected function shouldCreateBaseModel(): bool
    {
        $baseModel = config('ddd.base_model');

        // If the class exists, we don't need to create it.
        if (class_exists($baseModel)) {
            return false;
        }

        // If the class is outside of the domain layer, we won't attempt to create it.
        if (! DomainResolver::isDomainClass($baseModel)) {
            return false;
        }

        // At this point the class is probably a domain object, but we should
        // check if the expected path exists.
        if (file_exists(app()->basePath(DomainResolver::guessPathFromClass($baseModel)))) {
            return false;
        }

        return true;
    }
}
