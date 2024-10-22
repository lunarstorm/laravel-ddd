<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Symfony\Component\Console\Input\InputArgument;

class DomainBaseViewModelMakeCommand extends DomainGeneratorCommand
{
    protected $name = 'ddd:base-view-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a base view model';

    protected $type = 'Base View Model';

    protected function getArguments()
    {
        return [
            new InputArgument(
                'name',
                InputArgument::OPTIONAL,
                'The name of the base view model',
                'ViewModel'
            ),
        ];
    }

    protected function getStub()
    {
        return $this->resolveDddStubPath('base-view-model.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.view_model', 'ViewModels');
    }
}
