<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

trait QualifiesDomainModels
{
    protected function qualifyModel(string $model)
    {
        if ($domain = $this->blueprint->domain) {
            $domainModel = $domain->model($model);

            return $domainModel->fullyQualifiedName;
        }

        return parent::qualifyModel($model);
    }
}
