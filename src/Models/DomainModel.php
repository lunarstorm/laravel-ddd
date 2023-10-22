<?php

namespace Lunarstorm\LaravelDDD\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;

abstract class DomainModel
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return DomainFactory::factoryForModel(get_called_class());
    }
}
