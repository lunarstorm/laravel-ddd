<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ForwardsToDomainCommands;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

use function Laravel\Prompts\confirm;

class DomainControllerMakeCommand extends ControllerMakeCommand
{
    use ForwardsToDomainCommands,
        HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:controller';

    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (
            ! app()->runningUnitTests()
            && ! class_exists($modelClass)
            && confirm("A {$modelClass} model does not exist. Do you want to generate it?", default: true)
        ) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        $replace = $this->buildFormRequestReplacements($replace, $modelClass);

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);
    }

    protected function buildFormRequestReplacements(array $replace, $modelClass)
    {
        [$namespace, $storeRequestClass, $updateRequestClass] = [
            'Illuminate\\Http',
            'Request',
            'Request',
        ];

        if ($this->option('requests')) {
            $namespace = $this->domain->namespaceFor('request', $this->getNameInput());

            [$storeRequestClass, $updateRequestClass] = $this->generateFormRequests(
                $modelClass,
                $storeRequestClass,
                $updateRequestClass
            );
        }

        $namespacedRequests = $namespace.'\\'.$storeRequestClass.';';

        if ($storeRequestClass !== $updateRequestClass) {
            $namespacedRequests .= PHP_EOL.'use '.$namespace.'\\'.$updateRequestClass.';';
        }

        return array_merge($replace, [
            '{{ storeRequest }}' => $storeRequestClass,
            '{{storeRequest}}' => $storeRequestClass,
            '{{ updateRequest }}' => $updateRequestClass,
            '{{updateRequest}}' => $updateRequestClass,
            '{{ namespacedStoreRequest }}' => $namespace.'\\'.$storeRequestClass,
            '{{namespacedStoreRequest}}' => $namespace.'\\'.$storeRequestClass,
            '{{ namespacedUpdateRequest }}' => $namespace.'\\'.$updateRequestClass,
            '{{namespacedUpdateRequest}}' => $namespace.'\\'.$updateRequestClass,
            '{{ namespacedRequests }}' => $namespacedRequests,
            '{{namespacedRequests}}' => $namespacedRequests,
        ]);
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if($this->isUsingPublishedStub()){
            return $stub;
        }

        $replace = [];

        $appRootNamespace = $this->laravel->getNamespace();
        $pathToAppBaseController = parent::getPath("Http\Controllers\Controller");

        $baseControllerExists = file_exists($pathToAppBaseController);

        if ($baseControllerExists) {
            $controllerClass = class_basename($name);
            $replace["\nclass {$controllerClass}\n"] = "\nuse {$appRootNamespace}Http\Controllers\Controller;\n\nclass {$controllerClass} extends Controller\n";
        }

        $stub = str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );

        return $this->sortImports($stub);
    }
}
