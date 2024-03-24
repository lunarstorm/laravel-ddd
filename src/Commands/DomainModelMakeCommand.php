<?php

namespace Lunarstorm\LaravelDDD\Commands;

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

        if ($this->option('factory')) {
            $this->createFactory();
        }
    }

    protected function createBaseModelIfNeeded()
    {
        $baseModel = config('ddd.base_model');

        if (class_exists($baseModel)) {
            return;
        }

        $this->warn("Configured base model {$baseModel} doesn't exist.");

        // If the base model is out of scope, we won't attempt to create it
        // because we don't want to interfere with external folders.
        $allowedNamespacePrefixes = [
            $this->rootNamespace(),
        ];

        if (!str($baseModel)->startsWith($allowedNamespacePrefixes)) {
            return;
        }

        $domain = DomainResolver::guessDomainFromClass($baseModel);

        if (!$domain) {
            return;
        }

        $baseModelName = class_basename($baseModel);
        $baseModelPath = $this->getPath($baseModel);

        if (!file_exists($baseModelPath)) {
            $this->info("Generating {$baseModel}...");

            $this->call(DomainBaseModelMakeCommand::class, [
                '--domain' => $domain,
                'name' => $baseModelName,
            ]);
        }
    }

    protected function createFactory()
    {
        $this->call(DomainFactoryMakeCommand::class, [
            'name' => $this->getNameInput() . 'Factory',
            '--domain' => $this->domain->dotName,
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }
}
