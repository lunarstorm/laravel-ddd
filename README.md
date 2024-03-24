# Domain Driven Design toolkit for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)

Laravel-DDD is a toolkit to support domain driven design (DDD) patterns in Laravel applications. One of the pain points when adopting DDD is the inability to use Laravel's native `make:model` artisan command to properly generate domain models, since domain models are not intended to be stored in the `App/Models/*` namespace. This package aims to fill the gaps by providing an equivalent command, `ddd:model`, plus many more.

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

Command syntax:
```bash
# Specifying the domain as an option
php artisan ddd:{object} {name} --domain={domain}

# Specifying the domain as part of the name (short-hand syntax)
php artisan ddd:{object} {domain}:{name}
```

The following generators are currently available, shown using short-hand syntax:
```bash
# Generate a domain model
php artisan ddd:model {domain}:{name}

# Generate a domain model with factory
php artisan ddd:model {domain}:{name} -f
php artisan ddd:model {domain}:{name} --factory

# Generate a domain factory
php artisan ddd:factory {domain}:{name} [--model={model}]

# Generate a data transfer object
php artisan ddd:dto {domain}:{name}

# Generates a value object
php artisan ddd:value {domain}:{name}

# Generates a view model
php artisan ddd:view-model {domain}:{name}

# Generates an action
php artisan ddd:action {domain}:{name}

# Extended Commands 
# (extends Laravel's make:* generators and funnels the objects into the domain layer)
php artisan ddd:cast {domain}:{name}
php artisan ddd:channel {domain}:{name}
php artisan ddd:command {domain}:{name}
php artisan ddd:enum {domain}:{name} # Requires Laravel 11+
php artisan ddd:event {domain}:{name}
php artisan ddd:exception {domain}:{name}
php artisan ddd:job {domain}:{name}
php artisan ddd:listener {domain}:{name}
php artisan ddd:mail {domain}:{name}
php artisan ddd:notification {domain}:{name}
php artisan ddd:observer {domain}:{name}
php artisan ddd:policy {domain}:{name}
php artisan ddd:provider {domain}:{name}
php artisan ddd:resource {domain}:{name}
php artisan ddd:rule {domain}:{name}
php artisan ddd:scope {domain}:{name}
```

Examples:
```bash
php artisan ddd:model Invoicing:LineItem # Domain/Invoicing/Models/LineItem
php artisan ddd:model Invoicing:LineItem -f # Domain/Invoicing/Models/LineItem + Database/Factories/Invoicing/LineItemFactory
php artisan ddd:factory Invoicing:LineItemFactory # Database/Factories/Invoicing/LineItemFactory
php artisan ddd:dto Invoicing:LinePayload # Domain/Invoicing/Data/LinePayload
php artisan ddd:value Shared:Percentage # Domain/Shared/ValueObjects/Percentage
php artisan ddd:view-model Invoicing:ShowInvoiceViewModel # Domain/Invoicing/ViewModels/ShowInvoiceViewModel
php artisan ddd:action Invoicing:SendInvoiceToCustomer # Domain/Invoicing/Actions/SendInvoiceToCustomer
```

Subdomains (nested domains) can be specified with dot notation:
```bash
php artisan ddd:model Invoicing.Customer:CustomerInvoice # Domain/Invoicing/Customer/Models/CustomerInvoice
php artisan ddd:factory Invoicing.Customer:CustomerInvoice # Database/Factories/Invoicing/Customer/CustomerInvoiceFactory
# (supported by all generator commands)
```

This package ships with opinionated (but sensible) configuration defaults. If you need to customize, you may do so by publishing the config file and generator stubs as needed:

```bash
php artisan vendor:publish --tag="ddd-config"
php artisan vendor:publish --tag="ddd-stubs"
```
Note that the extended commands do not publish ddd-specific stubs, and inherit the respective application-level stubs published by Laravel.

This is the content of the published config file (`ddd.php`):

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Domain Path
    |--------------------------------------------------------------------------
    |
    | The path to the domain folder relative to the application root.
    |
    */
    'domain_path' => 'src/Domain',

    /*
    |--------------------------------------------------------------------------
    | Domain Namespace
    |--------------------------------------------------------------------------
    |
    | The root domain namespace.
    |
    */
    'domain_namespace' => 'Domain',

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
        'model' => 'Models',
        'data_transfer_object' => 'Data',
        'view_model' => 'ViewModels',
        'value_object' => 'ValueObjects',
        'action' => 'Actions',
        'cast' => 'Casts',
        'channel' => 'Channels',
        'command' => 'Commands',
        'enum' => 'Enums',
        'event' => 'Events',
        'exception' => 'Exceptions',
        'job' => 'Jobs',
        'listener' => 'Listeners',
        'mail' => 'Mail',
        'notification' => 'Notifications',
        'observer' => 'Observers',
        'policy' => 'Policies',
        'provider' => 'Providers',
        'resource' => 'Resources',
        'rule' => 'Rules',
        'scope' => 'Scopes',
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
