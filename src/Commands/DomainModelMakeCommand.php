<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Symfony\Component\Console\Input\InputOption;

class DomainModelMakeCommand extends DomainGeneratorCommand
{
    protected $name = 'ddd:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a domain model';

    protected $type = 'Model';

    protected function getOptions()
    {
        return [
            ...parent::getOptions(),
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the domain model'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration for the domain model'],
            ['test', 't', InputOption::VALUE_NONE, 'Generate an accompanying PHPUnit test for the model'], // TDOD
            ['pest', 'tpa', InputOption::VALUE_NONE, 'Generate an accompanying Pest test for the model'], // TDOD
            ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['policy', 'p', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, and policy classes for the model'],
        ];
    }

    protected function getStub()
    {
        return $this->resolveStubPath('model.php.stub');
    }

    protected function preparePlaceholders(): array
    {
        $baseClass = config('ddd.base_model');
        $baseClassName = class_basename($baseClass);

        return [
            'extends' => filled($baseClass) ? " extends {$baseClassName}" : '',
            'baseClassImport' => filled($baseClass) ? "use {$baseClass};" : '',
        ];
    }

    public function handle()
    {
        $this->createBaseModelIfNeeded();

        parent::handle();

        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('policy', true);
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }
        if ($this->option('migration')) {
            $this->createMigration();
        }
        if ($this->option('seed')) {
            $this->createSeeder();
        }
        if ($this->option('policy')) {
            $this->createPolicy();
        }
    }

    protected function createBaseModelIfNeeded()
    {
        if (! $this->shouldCreateModel()) {
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

    protected function shouldCreateModel(): bool
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

    protected function createFactory(): void
    {
        $this->call(DomainFactoryMakeCommand::class, [
            'name' => $this->getNameInput().'Factory',
            '--domain' => $this->domain->dotName,
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    protected function createMigration(): void
    {
        $tableName = Str::snake(Str::pluralStudly(class_basename($this->getNameInput())));

        $this->call('ddd:migration', [
            'name' => 'create_'.$tableName.'_table',
            '--domain' => $this->domain->dotName,
            '--create' => $tableName,
        ]);
    }

    protected function createPolicy(): void
    {
        $policyName = Str::studly(class_basename($this->argument('name'))).'Policy';

        $this->call(DomainPolicyMakeCommand::class, [
            'name' => $policyName,
            '--domain' => $this->domain->dotName,
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    private function createSeeder()
    {
        $seederName = Str::studly(class_basename($this->getNameInput())).'Seeder';

        $this->call(DomainSeederMakeCommand::class, [
            'name' => $seederName,
            '--domain' => $this->domain->dotName,
        ]);
    }
}
