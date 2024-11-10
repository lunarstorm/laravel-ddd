<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\InteractsWithStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainFactoryMakeCommand extends FactoryMakeCommand
{
    use HasDomainStubs,
        InteractsWithStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:factory';

    protected function getStub()
    {
        return $this->resolveDddStubPath('factory.stub');
    }

    protected function getNamespace($name)
    {
        return $this->domain->namespaceFor('factory');
    }

    protected function preparePlaceholders(): array
    {
        $domain = $this->domain;

        $name = $this->getNameInput();

        $modelName = $this->option('model') ?: $this->guessModelName($name);

        $domainModel = $domain->model($modelName);

        $domainFactory = $domain->factory($name);

        return [
            'namespacedModel' => $domainModel->fullyQualifiedName,
            'model' => class_basename($domainModel->fullyQualifiedName),
            'factory' => $domainFactory->name,
            'namespace' => $domainFactory->namespace,
        ];
    }

    protected function guessModelName($name)
    {
        if (str_ends_with($name, 'Factory')) {
            $name = substr($name, 0, -7);
        }

        return $this->domain->model(class_basename($name))->name;
    }
}
