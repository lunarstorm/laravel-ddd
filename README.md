# Laravel toolkit for Domain Driven Design

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)

Laravel-DDD is a toolkit to support domain driven design (DDD) patterns in Laravel applications. One of the pain points when adopting DDD is the inability to use Laravel's native `make:model` artisan command to properly generate domain models, since domain models are not intended to be stored in the `App/Models/*` namespace. This package aims to fill the gaps by providing an equivalent command, `ddd:make:model`, plus a few more.

## Installation

You can install the package via composer:

```bash
composer require lunarstorm/laravel-ddd
```

## Usage

The following generator commands are currently available:

```bash
# Generate a domain model
php artisan ddd:make:model {domain} {name}

# Generate a data transfer object
php artisan ddd:make:dto {domain} {name}

# Generates a value object
php artisan ddd:make:value {domain} {name}

# Generates a view model
php artisan ddd:make:view-model {domain} {name}
```
Examples:
```bash
php artisan ddd:make:model Invoicing LineItem # Domains/Invoicing/Models/LineItem
php artisan ddd:make:dto Invoicing LinePayload # Domains/Invoicing/Data/LinePayload
php artisan ddd:make:value Shared Percentage # Domains/Shared/ValueObjects/Percentage
php artisan ddd:make:value Invoicing ShowInvoiceViewModel # Domains/Invoicing/ViewModels/ShowInvoiceViewModel
```

This package ships with opinionated (but sensible) configuration defaults. If you need to customize, you may do so by publishing the config file and generator stubs:

```bash
php artisan vendor:publish --tag="ddd-config"
php artisan vendor:publish --tag="ddd-stubs"
```

This is the content of the published config file (`ddd.php`):

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | This value contains paths to the layers of the application in the context
    | of domain driven design, relative to the base folder of the application.
    |
    */

    'paths' => [
        //
        // Path to the Domain layer.
        //
        'domains' => 'src/Domains',

        //
        // Path to modules in the application layer. This is an extension of
        // domain driven design applied to the application layer, bundling
        // application objects (Controllers, Resources, Requests) in a
        // more modular fashion.
        //
        // e.g., app/Modules/Invoicing/Controllers/*
        //       app/Modules/Invoicing/Resources/*
        //       app/Modules/Invoicing/Requests/*
        //
        'modules' => 'app/Modules',
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Object Namespaces
    |--------------------------------------------------------------------------
    |
    | This value contains the default namespaces of generated domain
    | objects relative to the domain namespace of which the object
    | belongs to.
    |
    | e.g., Domains/Invoicing/Models/*
    |       Domains/Invoicing/Data/*
    |       Domains/Invoicing/ViewModels/*
    |       Domains/Invoicing/ValueObjects/*
    |
    */
    'namespaces' => [
        //
        // Models
        //
        'models' => 'Models',

        //
        // Data Transfer Objects (DTO)
        //
        'data_transfer_objects' => 'Data',

        //
        // View Models
        //
        'view_models' => 'ViewModels',

        //
        // Value Objects
        //
        'value_objects' => 'ValueObjects',
    ]
];
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jasper Tey](https://github.com/JasperTey)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
