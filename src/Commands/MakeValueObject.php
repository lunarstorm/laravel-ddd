<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeValueObject extends DomainGeneratorCommand
{
    protected $name = 'ddd:value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a value object';

    protected $type = 'Value Object';

    protected function getArguments()
    {
        return [
            ...parent::getArguments(),

            new InputArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the value object',
            )
        ];
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.value_objects', 'ValueObjects');
    }

    protected function getStub()
    {
        return $this->resolveStubPath('value-object.php.stub');
    }
}
