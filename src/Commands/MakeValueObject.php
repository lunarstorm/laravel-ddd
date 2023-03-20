<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

class MakeValueObject extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:value {domain} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a value object';

    protected $type = 'ValueObject';

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.value_objects', 'ValueObjects');
    }

    protected function getStub()
    {
        return $this->resolveStubPath('value-object.php.stub');
    }
}
