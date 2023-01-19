<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

class MakeViewModel extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:make:view-model {domain} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a view model';

    protected $type = 'ViewModel';

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.view_models', 'ViewModels');
    }

    protected function getStub()
    {
        return $this->resolveStubPath('components/view-model.php.stub');
    }
}
