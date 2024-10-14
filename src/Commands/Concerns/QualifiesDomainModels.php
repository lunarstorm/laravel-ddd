<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;

trait QualifiesDomainModels
{
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        // return $this->qualifyClass(
        //     $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        // );
        return $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name;
    }

    protected function qualifyModel(string $model)
    {
        if($domain = $this->domain) {
            $domainModel = $domain->model($model);

            return $domainModel->fullyQualifiedName;
        }

        return parent::qualifyModel($model);
    }
}
