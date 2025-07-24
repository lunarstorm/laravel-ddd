<?php

namespace Lunarstorm\LaravelDDD\Factories;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @template TFactory of \Illuminate\Database\Eloquent\Factories\Factory
 */
trait HasDomainFactory
{
    /** @use HasFactory<TFactory> */
    use HasFactory;

    /**
     * @return ?TFactory
     */
    protected static function newFactory()
    {
        return DomainFactory::factoryForModel(get_called_class());
    }
}
