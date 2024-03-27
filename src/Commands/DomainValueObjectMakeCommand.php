<?php

namespace Lunarstorm\LaravelDDD\Commands;

class DomainValueObjectMakeCommand extends DomainGeneratorCommand
{
    protected $name = 'ddd:value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a value object';

    protected $type = 'Value Object';

    protected function configure()
    {
        $this->setAliases([
            'ddd:value-object',
            'ddd:valueobject',
        ]);

        parent::configure();
    }

    protected function getStub()
    {
        return $this->resolveStubPath('value-object.php.stub');
    }
}
