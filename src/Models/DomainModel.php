<?php

namespace Lunarstorm\LaravelDDD\Models;

use Illuminate\Database\Eloquent\Model;
use Lunarstorm\LaravelDDD\Factories\HasDomainFactory;

abstract class DomainModel extends Model
{
    use HasDomainFactory;

    protected $guarded = [];
}
