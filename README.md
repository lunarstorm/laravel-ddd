# Domain Driven Design toolkit for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)

Laravel-DDD is a toolkit to support domain driven design (DDD) patterns in Laravel applications. One of the pain points when adopting DDD is the inability to use Laravel's native `make:model` artisan command to properly generate domain models, since domain models are not intended to be stored in the `App/Models/*` namespace. This package aims to fill the gaps by providing an equivalent command, `ddd:model`, plus a few more.

> :warning: **Disclaimer**: While this package is publicly available for installation, it is subject to frequent design changes as it evolves towards a stable v1.0 release. It is currently being tested and fine tuned within Lunarstorm's client projects.

## Installation

You can install the package via composer:

```bash
composer require lunarstorm/laravel-ddd
```

You may then initialize the package using the `ddd:install` artisan command. This command will publish the config file, register the domain path in your project's composer.json psr-4 autoload configuration on your behalf, and allow you to publish generator stubs for customization if needed.
```bash
php artisan ddd:install
```

## Usage

The following generator commands are currently available:

```bash
# Generate a domain model
php artisan ddd:model {domain} {name}

# Generate a domain model with factory
php artisan ddd:model {domain} {name} -f
php artisan ddd:model {domain} {name} --factory

# Generate a domain factory
php artisan ddd:factory {domain} {name} [--model={model}]

# Generate a data transfer object
php artisan ddd:dto {domain} {name}

# Generates a value object
php artisan ddd:value {domain} {name}

# Generates a view model
php artisan ddd:view-model {domain} {name}

# Generates an action
php artisan ddd:action {domain} {name}
```
Examples:
```bash
php artisan ddd:model Invoicing LineItem # Domain/Invoicing/Models/LineItem
php artisan ddd:model Invoicing LineItem -f # Domain/Invoicing/Models/LineItem + Database/Factories/Invoicing/LineItemFactory
php artisan ddd:factory Invoicing LineItemFactory # Database/Factories/Invoicing/LineItemFactory
php artisan ddd:dto Invoicing LinePayload # Domain/Invoicing/Data/LinePayload
php artisan ddd:value Shared Percentage # Domain/Shared/ValueObjects/Percentage
php artisan ddd:view-model Invoicing ShowInvoiceViewModel # Domain/Invoicing/ViewModels/ShowInvoiceViewModel
php artisan ddd:action Invoicing SendInvoiceToCustomer # Domain/Invoicing/Actions/SendInvoiceToCustomer
```

This package ships with opinionated (but sensible) configuration defaults. If you need to customize, you may do so by publishing the config file and generator stubs as needed:

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
        'domains' => 'src/Domain',
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
    | e.g., Domain/Invoicing/Models/*
    |       Domain/Invoicing/Data/*
    |       Domain/Invoicing/ViewModels/*
    |       Domain/Invoicing/ValueObjects/*
    |       Domain/Invoicing/Actions/*
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

        //
        // Actions
        //
        'actions' => 'Actions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Base Model
    |--------------------------------------------------------------------------
    |
    | The base class which generated domain models should extend. By default,
    | generated domain models will extend `Domain\Shared\Models\BaseModel`,
    | which will be created if it doesn't already exist.
    |
    */
    'base_model' => 'Domain\Shared\Models\BaseModel',

    /*
    |--------------------------------------------------------------------------
    | Base DTO
    |--------------------------------------------------------------------------
    |
    | The base class which generated data transfer objects should extend. By
    | default, generated DTOs will extend `Spatie\LaravelData\Data` from
    | Spatie's Laravel-data package, a highly recommended data object
    | package to work with.
    |
    */
    'base_dto' => 'Spatie\LaravelData\Data',

    /*
    |--------------------------------------------------------------------------
    | Base ViewModel
    |--------------------------------------------------------------------------
    |
    | The base class which generated view models should extend. By default,
    | generated domain models will extend `Domain\Shared\ViewModels\BaseViewModel`,
    | which will be created if it doesn't already exist.
    |
    */
    'base_view_model' => 'Domain\Shared\ViewModels\ViewModel',

    /*
    |--------------------------------------------------------------------------
    | Base Action
    |--------------------------------------------------------------------------
    |
    | The base class which generated action objects should extend. By default,
    | generated actions are based on the `lorisleiva/laravel-actions` package
    | and do not extend anything.
    |
    */
    'base_action' => null,
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
