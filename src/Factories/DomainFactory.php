<?php

namespace Lunarstorm\LaravelDDD\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\DomainResolver;

abstract class DomainFactory extends Factory
{
    /**
     * Get the domain namespace.
     *
     * @return string
     */
    protected static function domainNamespace()
    {
        return Str::finish(DomainResolver::getConfiguredDomainNamespace(), '\\');
    }

    /**
     * Get the factory name for the given domain model name.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelName
     * @return null|class-string<\Illuminate\Database\Eloquent\Factories\Factory>
     */
    public static function resolveFactoryName(string $modelName)
    {
        $resolver = function (string $modelName) {
            $domainNamespace = static::domainNamespace();
            $modelNamespace = config('ddd.namespaces.models');

            // Expected domain model FQN:
            // {DomainNamespace}\{Domain}\{ModelNamespace}\{Model}

            if (! Str::startsWith($modelName, $domainNamespace)) {
                // Not a domain model
                return null;
            }

            $domain = str($modelName)
                ->after($domainNamespace)
                ->beforeLast($modelNamespace)
                ->trim('\\')
                ->toString();

            $modelBaseName = class_basename($modelName);

            return static::$namespace."{$domain}\\{$modelBaseName}Factory";
        };

        return $resolver($modelName);
    }
}
