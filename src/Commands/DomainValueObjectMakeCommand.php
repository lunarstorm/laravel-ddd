<?php

namespace Tey\LaravelDDD\Commands;

use Tey\LaravelDDD\Commands\Concerns\HasDomainStubs;

class DomainValueObjectMakeCommand extends DomainGeneratorCommand
{
    use HasDomainStubs;

    protected $name = 'ddd:value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a value object';

    protected $type = 'Value Object';

    protected function configure(): void
    {
        $this->setAliases([
            'ddd:value-object',
            'ddd:valueobject',
        ]);

        parent::configure();
    }

    protected function getStub()
    {
        return $this->resolveDddStubPath('value-object.stub');
    }
}
