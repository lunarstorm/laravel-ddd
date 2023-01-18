<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

class MakeValueObject extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:make:value {domain} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a value object';

    protected $type = 'ValueObject';

    protected function getStub()
    {
        return $this->resolveStubPath('components/value-object.php.stub');
    }

    public function handle()
    {
        parent::handle();
    }
}
