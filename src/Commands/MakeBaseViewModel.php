<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Symfony\Component\Console\Input\InputArgument;

class MakeBaseViewModel extends DomainGeneratorCommand
{
    use ResolvesDomainFromInput;

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
        return $this->resolveStubPath('base-view-model.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.view_model', 'ViewModels');
    }
}
