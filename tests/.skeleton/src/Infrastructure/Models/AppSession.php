<?php

namespace Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Lunarstorm\LaravelDDD\Factories\HasDomainFactory;

class AppSession extends Model
{
    use HasDomainFactory;

    protected static $secret = null;

    public static function setSecret($secret): void
    {
        self::$secret = $secret;
    }

    public static function getSecret(): ?string
    {
        return self::$secret;
    }
}
