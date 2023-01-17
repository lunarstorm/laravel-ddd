<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

class MakeDTO extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:make:dto {domain} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a data transfer object';

    protected $type = 'DataTransferObject';

    protected function getStub()
    {
        return resource_path('/stubs/ddd/dto.php.stub');
    }

    public function handle()
    {
        parent::handle();

        // $this->alreadyExists();
    }
}
