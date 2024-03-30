<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Symfony\Component\Console\Input\InputOption;

class DomainFactoryMakeCommand extends DomainGeneratorCommand
{
    protected $name = 'ddd:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a domain model factory';

    protected $type = 'Factory';

    protected function getOptions()
    {
        return [
            ...parent::getOptions(),
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
        ];
    }

    protected function getStub()
    {
        return $this->resolveStubPath('factory.php.stub');
    }

    protected function getPath($name)
    {
        if (! str_ends_with($name, 'Factory')) {
            $name .= 'Factory';
        }

        return parent::getPath($name);
    }

    protected function getFactoryName()
    {
        $name = $this->getNameInput();

        return str_ends_with($name, 'Factory')
            ? substr($name, 0, -7)
            : $name;
    }

    protected function preparePlaceholders(): array
    {
        $domain = $this->domain;

        $name = $this->getNameInput();

        $modelName = $this->option('model') ?: $this->guessModelName($name);

        $domainModel = $domain->model($modelName);

        $domainFactory = $domain->factory($name);

        // dump('preparing placeholders', [
        //     'name' => $name,
        //     'modelName' => $modelName,
        //     'domainFactory' => $domainFactory,
        // ]);

        return [
            'namespacedModel' => $domainModel->fullyQualifiedName,
            'model' => class_basename($domainModel->fullyQualifiedName),
            'factory' => $this->getFactoryName(),
            'namespace' => $domainFactory->namespace,
        ];
    }

    protected function guessModelName($name)
    {
        if (str_ends_with($name, 'Factory')) {
            $name = substr($name, 0, -7);
        }

        return $this->domain->model($name)->name;
    }
}
