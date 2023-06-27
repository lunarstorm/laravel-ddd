<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Symfony\Component\Console\Input\InputArgument;

class MakeAction extends DomainGeneratorCommand
{
    protected $name = 'ddd:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an action';

    protected $type = 'Action';

    protected function getArguments()
    {
        return [
            ...parent::getArguments(),

            new InputArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the Action',
            ),
        ];
    }

    protected function getStub()
    {
        return $this->resolveStubPath('action.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.actions', 'Actions');
    }

    protected function preparePlaceholders(): array
    {
        $baseClass = config('ddd.base_action');

        return [
            'extends' => filled($baseClass) ? " extends {$baseClass}" : '',
        ];
    }
}
