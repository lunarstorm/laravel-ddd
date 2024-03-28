<?php

namespace Lunarstorm\LaravelDDD\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\ValueObjects\DomainObject;

abstract class DomainFactory extends Factory
{
    /**
     * Get the domain namespace.
     *
     * @return string
     */
    protected static function domainNamespace()
    {
        return Str::finish(DomainResolver::domainRootNamespace(), '\\');
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
            $model = DomainObject::fromClass($modelName, 'model');

            if (! $model) {
                // Not a domain model
                return null;
            }

            // First try resolving as a factory class in the domain layer
            if (class_exists($factoryClass = DomainResolver::getDomainObjectNamespace($model->domain, 'factory', "{$model->name}Factory"))) {
                return $factoryClass;
            }

            // Otherwise, fallback to the the standard location under /database/factories
            return static::$namespace."{$model->domain}\\{$model->name}Factory";
        };

        return $resolver($modelName);
    }
}
