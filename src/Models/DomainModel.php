<?php

namespace Tey\LaravelDDD\Models;

use Illuminate\Database\Eloquent\Model;
use Tey\LaravelDDD\Factories\HasDomainFactory;

abstract class DomainModel extends Model
{
    use HasDomainFactory;

    protected $guarded = [];
}
