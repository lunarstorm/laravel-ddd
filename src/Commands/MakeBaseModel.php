<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Symfony\Component\Console\Input\InputArgument;

class MakeBaseModel extends DomainGeneratorCommand
{
    protected $name = 'ddd:base-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a base domain model';

    protected $type = 'Base Model';

    protected function getArguments()
    {
        return [
            new InputArgument(
                'name',
                InputArgument::OPTIONAL,
                'The name of the base model',
                'BaseModel'
            ),
        ];
    }

    protected function getStub()
    {
        return $this->resolveStubPath('base-model.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.model', 'Models');
    }
}
