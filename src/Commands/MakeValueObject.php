<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class MakeValueObject extends DomainGeneratorCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a value object';

    protected $type = 'Value Object';

    protected function getStub()
    {
        return $this->resolveStubPath('value-object.php.stub');
    }
}
