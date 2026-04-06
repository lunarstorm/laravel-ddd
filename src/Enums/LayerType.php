<?php

namespace Tey\LaravelDDD\Enums;

enum LayerType: string
{
    case Domain = 'Domain';
    case Application = 'Application';
    case Custom = 'Custom';
}
