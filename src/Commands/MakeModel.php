<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

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
        return $this->resolveStubPath('components/model.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.models', 'Models');
    }

    public function handle()
    {
        $baseModel = config('ddd.base_model');

        if (! class_exists($baseModel)) {
            $this->warn("Base model {$baseModel} doesn't exist, generating...");
        }

        parent::handle();
    }
}
