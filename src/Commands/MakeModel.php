<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeModel extends DomainGeneratorCommand
{
    protected $name = 'ddd:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a domain model';

    protected $type = 'Model';

    protected function getArguments()
    {
        return [
            ...parent::getArguments(),

            new InputArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the model',
            ),
        ];
    }

    protected function getOptions()
    {
        return [
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the domain model'],
        ];
    }

    protected function getStub()
    {
        return $this->resolveStubPath('model.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.models', 'Models');
    }

    protected function preparePlaceholders(): array
    {
        $baseClass = config('ddd.base_model');
        $baseClassName = class_basename($baseClass);

        return [
            'extends' => filled($baseClass) ? " extends {$baseClassName}" : '',
        ];
    }

    public function handle()
    {
        $this->createBaseModelIfNeeded(config('ddd.base_model'));

        parent::handle();

        if ($this->option('factory')) {
            $this->createFactory();
        }
    }

    protected function createBaseModelIfNeeded($baseModel)
    {
        if (class_exists($baseModel)) {
            return;
        }

        $this->warn("Configured base model {$baseModel} doesn't exist.");

        // If the base model is out of scope, we won't attempt to create it
        // because we don't want to interfere with outside folders.
        $allowedRootNamespaces = [
            $this->rootNamespace(),
        ];

        if (! str($baseModel)->startsWith($allowedRootNamespaces)) {
            return;
        }

        $domain = DomainResolver::fromClass($baseModel);

        if (! $domain) {
            return;
        }

        $baseModelName = class_basename($baseModel);
        $baseModelPath = $this->getPath($baseModel);

        if (! file_exists($baseModelPath)) {
            $this->info("Generating {$baseModel}...");

            $this->call(MakeBaseModel::class, [
                'domain' => $domain->domain,
                'name' => $baseModelName,
            ]);
        }
    }

    protected function createFactory()
    {
        $this->call(MakeFactory::class, [
            'domain' => $this->getDomain(),
            'name' => $this->getNameInput().'Factory',
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }
}
