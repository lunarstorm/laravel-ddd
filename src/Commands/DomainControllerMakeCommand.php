<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ForwardsToDomainCommands;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;

class DomainControllerMakeCommand extends ControllerMakeCommand
{
    use ForwardsToDomainCommands,
        HasDomainStubs,
        ResolvesDomainFromInput;

    protected $name = 'ddd:controller';

    protected function buildFormRequestReplacements(array $replace, $modelClass)
    {
        [$namespace, $storeRequestClass, $updateRequestClass] = [
            'Illuminate\\Http',
            'Request',
            'Request',
        ];

        if ($this->option('requests')) {
            $namespace = $this->blueprint->getNamespaceFor('request', $this->getNameInput());

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

        if ($this->isUsingPublishedStub()) {
            return $stub;
        }

        $replace = [];

        $appRootNamespace = $this->laravel->getNamespace();
        $pathToAppBaseController = app_path('Http/Controllers/Controller.php');

        $baseControllerExists = $this->files->exists($pathToAppBaseController);

        if ($baseControllerExists) {
            $controllerClass = class_basename($name);
            $fullyQualifiedBaseController = "{$appRootNamespace}Http\Controllers\Controller";
            $namespaceLine = "namespace {$this->getNamespace($name)};";
            $replace[$namespaceLine.PHP_EOL] = $namespaceLine.PHP_EOL.PHP_EOL."use {$fullyQualifiedBaseController};";
            $replace["class {$controllerClass}".PHP_EOL] = "class {$controllerClass} extends Controller".PHP_EOL;
        }

        $stub = str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );

        return $this->sortImports($stub);
    }
}
