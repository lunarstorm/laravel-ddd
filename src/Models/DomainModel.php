<?php

namespace Lunarstorm\LaravelDDD\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunarstorm\LaravelDDD\Factories\DomainFactory;

abstract class DomainModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return DomainFactory::factoryForModel(get_called_class());
    }
}
