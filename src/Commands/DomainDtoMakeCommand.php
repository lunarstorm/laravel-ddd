<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;

class DomainDtoMakeCommand extends DomainGeneratorCommand
{
    use HasDomainStubs;
    
    protected $name = 'ddd:dto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a data transfer object';

    protected $type = 'Data Transfer Object';

    protected function configure()
    {
        $this->setAliases([
            'ddd:data-transfer-object',
            'ddd:datatransferobject',
            'ddd:data',
        ]);

        parent::configure();
    }

    protected function getStub()
    {
        return $this->resolveDddStubPath('dto.stub');
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
