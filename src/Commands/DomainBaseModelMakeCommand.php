<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Symfony\Component\Console\Input\InputArgument;

class DomainBaseModelMakeCommand extends DomainGeneratorCommand
{
    use HasDomainStubs;

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
        return $this->resolveDddStubPath('base-model.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.model', 'Models');
    }
}
