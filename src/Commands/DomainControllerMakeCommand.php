<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Lunarstorm\LaravelDDD\Commands\Concerns\ForwardsToDomainCommands;
use Lunarstorm\LaravelDDD\Commands\Concerns\HasDomainStubs;
use Lunarstorm\LaravelDDD\Commands\Concerns\ResolvesDomainFromInput;
use Lunarstorm\LaravelDDD\Support\Path;

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

        // Handle Laravel 10 side effect
        // todo: deprecated since L10 is no longer supported.
        if (str($stub)->contains($invalidUse = "use {$this->getNamespace($name)}\Http\Controllers\Controller;\n")) {
            $laravel10Replacements = [
                ' extends Controller' => '',
                $invalidUse => '',
            ];

            $stub = str_replace(
                array_keys($laravel10Replacements),
                array_values($laravel10Replacements),
                $stub
            );
        }

        $replace = [];

        $appRootNamespace = $this->laravel->getNamespace();
        $pathToAppBaseController = Path::normalize(app()->path('Http/Controllers/Controller.php'));

        $baseControllerExists = $this->files->exists($pathToAppBaseController);

        if ($baseControllerExists) {
            $controllerClass = class_basename($name);
            $fullyQualifiedBaseController = "{$appRootNamespace}Http\Controllers\Controller";
            $namespaceLine = "namespace {$this->getNamespace($name)};";
            $replace["{$namespaceLine}\n"] = "{$namespaceLine}\n\nuse {$fullyQualifiedBaseController};";
            $replace["class {$controllerClass}\n"] = "class {$controllerClass} extends Controller\n";
        }

        $stub = str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );

        return $this->sortImports($stub);
    }
}
