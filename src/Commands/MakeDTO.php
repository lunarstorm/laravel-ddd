<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Symfony\Component\Console\Input\InputArgument;

class MakeDTO extends DomainGeneratorCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:dto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a data transfer object';

    protected $type = 'Data Transfer Object';

    protected function getStub()
    {
        return $this->resolveStubPath('dto.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.data_transfer_object', 'Data');
    }

    protected function preparePlaceholders(): array
    {
        $baseClass = config('ddd.base_dto');

        return [
            'extends' => filled($baseClass) ? " extends {$baseClass}" : '',
        ];
    }
}
