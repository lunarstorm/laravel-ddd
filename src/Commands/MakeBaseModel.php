<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

class MakeBaseModel extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:make:base-model {domain} {name=BaseModel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a base domain model';

    protected $type = 'BaseModel';

    protected function getStub()
    {
        return $this->resolveStubPath('base-model.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.models', 'Models');
    }
}
