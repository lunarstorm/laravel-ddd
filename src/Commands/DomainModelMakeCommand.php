<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Symfony\Component\Console\Input\InputOption;

class DomainModelMakeCommand extends ModelMakeCommand
{
    use ResolvesDomainFromInput;

    protected $name = 'ddd:model';

    protected function createFactory()
    {
        $this->call(DomainFactoryMakeCommand::class, [
            'name' => $this->getNameInput().'Factory',
            '--domain' => $this->domain->dotName,
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }
}
