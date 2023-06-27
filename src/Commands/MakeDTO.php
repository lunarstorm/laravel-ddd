<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeDTO extends DomainGeneratorCommand
{
    protected $name = 'ddd:dto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a data transfer object';

    protected $type = 'Data Transfer Object';

    protected function getArguments()
    {
        return [
            ...parent::getArguments(),

            new InputArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the DTO',
            ),
        ];
    }

    protected function getStub()
    {
        return $this->resolveStubPath('dto.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.data_transfer_objects', 'Data');
    }

    protected function preparePlaceholders(): array
    {
        $baseClass = config('ddd.base_dto');

        return [
            'extends' => filled($baseClass) ? " extends {$baseClass}" : '',
        ];
    }
}
