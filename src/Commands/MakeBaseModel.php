<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

class MakeBaseModel extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:make:base-model {domain} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a base domain model';

    protected $type = 'BaseModel';

    protected function getStub()
    {
        return $this->resolveStubPath('components/base-model.php.stub');
    }
}
