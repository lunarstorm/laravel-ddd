<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;

class MakeBaseViewModel extends DomainGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:base-view-model {domain} {name=ViewModel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a base view model';

    protected $type = 'ViewModel';

    protected function getStub()
    {
        return $this->resolveStubPath('base-view-model.php.stub');
    }

    protected function getRelativeDomainNamespace(): string
    {
        return config('ddd.namespaces.view_models', 'ViewModels');
    }
}
