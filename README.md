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

You may initialize the package using the `ddd:install` artisan command. This will publish the [config file](#config-file), register the domain path in your project's composer.json psr-4 autoload configuration on your behalf, and allow you to publish generator stubs for customization if needed.
```bash
php artisan ddd:install
```

### Deployment
In production, run `ddd:cache` during the deployment process to [optimize autoloading](#autoloading-in-production).
```bash
php artisan ddd:cache
```

### Version Compatibility
 Laravel        | LaravelDDD |                                                                                      |
:---------------|:-----------|:-------------------------------------------------------------------------------------|
 9.x - 10.24.x  | 0.x        | **[0.x README](https://github.com/lunarstorm/laravel-ddd/blob/v0.10.0/README.md)**   |
 10.25.x        | 1.x        |  
 11.x           | 1.x        |

### Upgrading from 0.x
- Update config to the [latest format](#config-file). A helper command `ddd:upgrade` is available to assist with this.
- Remove and re-publish stubs if applicable.
- In production, `ddd:cache` should be run during the deployment process. See the [Autoloading in Production](#autoloading-in-production) section for more details.

## Usage
### Syntax
All domain generator commands use the following syntax:
```bash
# Specifying the domain as an option
php artisan ddd:{object} {name} --domain={domain}

# Specifying the domain as part of the name (short-hand syntax)
php artisan ddd:{object} {domain}:{name}

# Not specifying the domain at all, which will then prompt
# prompt for it (with auto-completion)
php artisan ddd:{object} {name}
```

## Available Commands
### Generators
The following generators are currently available, shown using short-hand syntax:
```bash
# Generate a domain model
php artisan ddd:model Invoicing:Invoice

# Generate a domain model with factory
php artisan ddd:model Invoicing:Invoice -f
php artisan ddd:model Invoicing:Invoice --factory

# Generate a domain factory
php artisan ddd:factory Invoicing:InvoiceFactory
php artisan ddd:factory Invoicing:InvoiceFactory --model=Invoice # optionally specifying the model

# Generate a data transfer object
php artisan ddd:dto Invoicing:LineItemPayload

# Generates a value object
php artisan ddd:value Shared:DollarAmount

# Generates a view model
php artisan ddd:view-model Invoicing:ShowInvoiceViewModel

# Generates an action
php artisan ddd:action Invoicing:SendInvoiceToCustomer

# Extended Commands 
# These extend Laravel's respective make:* commands and places the objects into the domain layer
php artisan ddd:cast Invoicing:MoneyCast
php artisan ddd:channel Invoicing:InvoiceChannel
php artisan ddd:command Invoicing:InvoiceDeliver
php artisan ddd:event Invoicing:PaymentWasReceived
php artisan ddd:exception Invoicing:InvoiceNotFoundException
php artisan ddd:job Invoicing:GenerateInvoicePdf
php artisan ddd:listener Invoicing:HandlePaymentReceived
php artisan ddd:mail Invoicing:OverduePaymentReminderEmail
php artisan ddd:notification Invoicing:YourPaymentWasReceived
php artisan ddd:observer Invoicing:InvoiceObserver
php artisan ddd:policy Invoicing:InvoicePolicy
php artisan ddd:provider Invoicing:InvoiceServiceProvider
php artisan ddd:resource Invoicing:InvoiceResource
php artisan ddd:rule Invoicing:ValidPaymentMethod
php artisan ddd:scope Invoicing:ArchivedInvoicesScope

# Laravel 11+ only
php artisan ddd:class Invoicing:Support/InvoiceBuilder
php artisan ddd:enum Customer:CustomerType
php artisan ddd:interface Customer:Contracts/Invoiceable
php artisan ddd:trait Customer:Concerns/HasInvoices
```
Generated objects will be placed in the appropriate domain namespace as specified by `ddd.namespaces.*` in the [config file](#config-file).

### Other Commands
```bash
# Show a summary of current domains in the domain folder
php artisan ddd:list

# Cache domain manifests (used for autoloading)
php artisan ddd:cache

# Clear the domain cache
php artisan ddd:clear
```

## Advanced Usage
### Nested Objects
For any `ddd:*` generator command, nested objects can be specified with forward slashes.
```bash
php artisan ddd:model Invoicing:Payment/Transaction
# -> Domain\Invoicing\Models\Payment\Transaction

php artisan ddd:action Invoicing:Payment/ProcessTransaction
# -> Domain\Invoicing\Actions\Payment\ProcessTransaction

php artisan ddd:exception Invoicing:Payment/PaymentFailedException
# -> Domain\Invoicing\Exceptions\Payment\PaymentFailedException
```
This is essential for objects without a fixed namespace such as `class`, `interface`, `trait`, 
each of which have a blank namespace by default. In other words, these objects originate 
from the root of the domain.
```bash
php artisan ddd:class Invoicing:Support/InvoiceBuilder
# -> Domain\Invoicing\Support\InvoiceBuilder

php artisan ddd:interface Invoicing:Contracts/PayableByCreditCard
# -> Domain\Invoicing\Contracts\PayableByCreditCard

php artisan ddd:interface Invoicing:Models/Concerns/HasLineItems
# -> Domain\Invoicing\Models\Concerns\HasLineItems
```

### Overriding Configured Namespaces at Runtime
If for some reason you need to generate a domain object under a namespace different to what is configured in `ddd.namespaces.*`,
you may do so using an absolute name starting with `/`. This will generate the object from the root of the domain.
```bash
# The usual: generate a provider in the configured provider namespace
php artisan ddd:provider Invoicing:InvoiceServiceProvider 
# -> Domain\Invoicing\Providers\InvoiceServiceProvider

# Override the configured namespace at runtime
php artisan ddd:provider Invoicing:/InvoiceServiceProvider
# -> Domain\Invoicing\InvoiceServiceProvider

# Generate an event inside the Models namespace (hypothetical)
php artisan ddd:event Invoicing:/Models/EventDoesNotBelongHere
# -> Domain\Invoicing\Models\EventDoesNotBelongHere

# Deep nesting is supported
php artisan ddd:exception Invoicing:/Models/Exceptions/InvoiceNotFoundException
# -> Domain\Invoicing\Models\Exceptions\InvoiceNotFoundException
```

### Subdomains (nested domains)
Subdomains can be specified with dot notation wherever a domain option is accepted.
```bash
# Domain/Reporting/Internal/ViewModels/MonthlyInvoicesReportViewModel
php artisan ddd:view-model Reporting.Internal:MonthlyInvoicesReportViewModel

# Domain/Reporting/Customer/ViewModels/MonthlyInvoicesReportViewModel
php artisan ddd:view-model Reporting.Customer:MonthlyInvoicesReportViewModel

# (supported by all commands where a domain option is accepted)
```

## Customization
This package ships with opinionated (but sensible) configuration defaults. You may customize by publishing the [config file](#config-file) and generator stubs as needed:

```bash
php artisan vendor:publish --tag="ddd-config"
php artisan vendor:publish --tag="ddd-stubs"
```
Note that the extended commands do not publish ddd-specific stubs, and inherit the respective application-level stubs published by Laravel.

## Domain Autoloading and Discovery
Autoloading behaviour can be configured with the `ddd.autoload` configuration option. By default, domain providers, commands, policies, and factories are auto-discovered and registered.

```php
'autoload' => [
    'providers' => true,
    'commands' => true,
    'policies' => true,
    'factories' => true,
],
```
### Service Providers
When `ddd.autoload.providers` is enabled, any class within the domain layer extending `Illuminate\Support\ServiceProvider` will be auto-registered as a service provider.

### Console Commands
When `ddd.autoload.commands` is enabled, any class within the domain layer extending `Illuminate\Console\Command` will be auto-registered as a command when running in console.

### Policies
When `ddd.autoload.policies` is enabled, the package will register a custom policy discovery callback to resolve policy names for domain models, and fallback to Laravel's default for all other cases. If your application implements its own policy discovery using `Gate::guessPolicyNamesUsing()`, you should set `ddd.autoload.policies` to `false` to ensure it is not overridden.

### Factories
When `ddd.autoload.factories` is enabled, the package will register a custom factory discovery callback to resolve factory names for domain models, and fallback to Laravel's default for all other cases. Note that this does not affect domain models using the `Lunarstorm\LaravelDDD\Factories\HasDomainFactory` trait. Where this is useful is with regular models in the domain layer that use the standard `Illuminate\Database\Eloquent\Factories\HasFactory` trait.

If your application implements its own factory discovery using `Factory::guessFactoryNamesUsing()`, you should set `ddd.autoload.factories` to `false` to ensure it is not overridden.

### Ignoring Paths During Autoloading
To specify folders or paths that should be skipped during autoloading discovery, add them to the `ddd.autoload_ignore` configuration option. By default, the `Tests` and `Migrations` folders are ignored.
```php
'autoload_ignore' => [
    'Tests',
    'Database/Migrations',
],
```

Paths specified here are relative to the root of each domain. e.g., `src/Domain/Invoicing/{path-to-ignore}`. If more advanced filtering is needed, a callback can be registered using the `DDD::filterAutoloadPathsUsing(callback $filter)` in your AppServiceProvider's boot method:
```php
use Lunarstorm\LaravelDDD\Facades\DDD;
use Symfony\Component\Finder\SplFileInfo;

DDD::filterAutoloadPathsUsing(function (SplFileInfo $file) {
    if (basename($file->getRelativePathname()) === 'functions.php') {
        return false;
    }
});
```

### Disabling Autoloading
You may disable autoloading by setting the respective autoload options to `false` in the configuration file as needed, or by commenting out the autoload configuration entirely.
```php
// 'autoload' => [
//     'providers' => true,
//     'commands' => true,
//     'policies' => true,
//     'factories' => true,
// ],
```

<a name="autoloading-in-production"></a>

## Autoloading in Production
In production, you should cache the autoload manifests using the `ddd:cache` command as part of your application's deployment process. This will speed up the auto-discovery and registration of domain providers and commands. The `ddd:clear` command may be used to clear the cache if needed.

<a name="config-file"></a>

## Configuration File
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
    | e.g., Domain\Invoicing\Models\*
    |       Domain\Invoicing\Data\*
    |       Domain\Invoicing\ViewModels\*
    |       Domain\Invoicing\ValueObjects\*
    |       Domain\Invoicing\Actions\*
    |
    */
    'namespaces' => [
        'model' => 'Models',
        'data_transfer_object' => 'Data',
        'view_model' => 'ViewModels',
        'value_object' => 'ValueObjects',
        'action' => 'Actions',
        'cast' => 'Casts',
        'class' => '',
        'channel' => 'Channels',
        'command' => 'Commands',
        'enum' => 'Enums',
        'event' => 'Events',
        'exception' => 'Exceptions',
        'factory' => 'Database\Factories',
        'interface' => '',
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
        'trait' => '',
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

    /*
    |--------------------------------------------------------------------------
    | Autoloading
    |--------------------------------------------------------------------------
    |
    | Configure whether domain providers, commands, policies, and factories
    | should be auto-discovered and registered.
    |
    */
    'autoload' => [
        /**
         * When enabled, any class within the domain layer extending `Illuminate\Support\ServiceProvider`
         * will be auto-registered as a service provider
         */
        'providers' => true,

        /**
         * When enabled, any class within the domain layer extending `Illuminate\Console\Command`
         * will be auto-registered as a command when running in console.
         */
        'commands' => true,

        /**
         * When enabled, the package will register a custom policy discovery callback to resolve policy names
         * for domain models, and fallback to Laravel's default for all other cases.
         */
        'policies' => true,

        /**
         * When enabled, the package will register a custom factory discovery callback to resolve factory names
         * for domain models, and fallback to Laravel's default for all other cases.
         */
        'factories' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoload Ignore Folders
    |--------------------------------------------------------------------------
    |
    | Folders that should be skipped during autoloading discovery,
    | relative to the root of each domain.
    |
    | e.g., src/Domain/Invoicing/<folder-to-ignore>
    |
    | If more advanced filtering is needed, a callback can be registered
    | using the `DDD::filterAutoloadPathsUsing(callback $filter)` in
    | the AppServiceProvider's boot method.
    |
    */
    'autoload_ignore' => [
        'Tests',
        'Migrations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | The folder where the domain cache files will be stored. Used for domain
    | autoloading.
    |
    */
    'cache_directory' => 'bootstrap/cache/ddd',
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
