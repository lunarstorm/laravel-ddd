<?php

namespace Lunarstorm\LaravelDDD\Factories;

use Illuminate\Database\Eloquent\Factories\HasFactory;

trait HasDomainFactory
{
    use HasFactory;

    protected static function newFactory()
    {
        return DomainFactory::factoryForModel(get_called_class());
    }
}
