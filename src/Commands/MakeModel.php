<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

class MakeModel extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:make:model {domain} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a domain model';

    protected $type = 'Model';

    protected function getStub()
    {
        return resource_path('/stubs/ddd/model.php.stub');
    }

    public function handle()
    {
        parent::handle();

        // $this->alreadyExists();
    }
}
