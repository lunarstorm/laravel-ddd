# Domain Driven Design Toolkit for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)

Laravel-DDD is a toolkit to support domain driven design (DDD) in Laravel applications. One of the pain points when adopting DDD is the inability to use Laravel's native `make` commands to generate objects outside the `App\*` namespace. This package aims to fill the gaps by providing equivalent commands such as `ddd:model`, `ddd:dto`, `ddd:view-model` and many more.

## Installation
You can install the package via composer:

```bash
composer require lunarstorm/laravel-ddd
```

You may initialize the package using the `ddd:install` artisan command. This will publish the [config file](#config-file), register the domain path in your project's composer.json psr-4 autoload configuration on your behalf, and allow you to publish generator stubs for customization if needed.
```bash
php artisan ddd:install
```

### Configuration
For first-time installations, a config wizard is available to populate the `ddd.php` config file interactively:
```bash
php artisan ddd:config wizard
```
For existing installations with a config file published from a previous version, you may use the `ddd:config update` command to rebuild and merge it with the latest package copy:
```bash
php artisan ddd:config update
```
See [Configuration Utility](#config-utility) for details about other available options.

### Peer Dependencies
The following additional packages are suggested (but not required) while working with this package.
- Data Transfer Objects: [spatie/laravel-data](https://github.com/spatie/laravel-data)
- Actions: [lorisleiva/laravel-actions](https://github.com/lorisleiva/laravel-actions)

The default DTO and Action stubs of this package reference classes from these packages. If this doesn't apply to your application, you may [publish and customize the stubs](#customizing-stubs) accordingly.

### Deployment
In production, ensure you run `php artisan ddd:optimize` during the deployment process to [optimize autoloading](#autoloading-in-production). If you already run `php artisan optimize` in production, this will be handled automatically.

### Version Compatibility
 Laravel        | LaravelDDD |                                                                                      |
:---------------|:-----------|:-------------------------------------------------------------------------------------|
 9.x - 10.24.x  | 0.x        | **[0.x README](https://github.com/lunarstorm/laravel-ddd/blob/v0.10.0/README.md)**   |
 10.25.x        | 1.x        |  
 11.x           | 1.x        |
 11.43.x        | 2.x        |
 12.x           | 2.x        |

See **[UPGRADING](UPGRADING.md)** for more details about upgrading across different versions.

<a name="usage"></a>

## Usage
### Syntax
All domain generator commands use the following syntax:
```bash
# Specifying the domain as an option
php artisan ddd:{object} {name} --domain={domain}

# Specifying the domain as part of the name (short-hand syntax)
php artisan ddd:{object} {domain}:{name}

# Not specifying the domain at all, which will then 
# prompt for it (with auto-completion)
php artisan ddd:{object} {name}
```

## Available Commands
### Generators
The following generators are currently available:
| Command | Description | Usage |
|---|---|---|
| `ddd:model` | Generate a domain model | `php artisan ddd:model Invoicing:Invoice`<br> <br> Options:<br> `--migration\|-m`<br>  `--factory\|-f`<br> `--seed\|-s`<br> `--controller --resource --requests\|-crR`<br> `--policy`<br> `-mfsc`<br> `--all\|-a`<br> `--pivot\|-p`<br> |
| `ddd:factory` | Generate a domain factory | `php artisan ddd:factory Invoicing:InvoiceFactory` |
| `ddd:dto` | Generate a data transfer object | `php artisan ddd:dto Invoicing:LineItemPayload` |
| `ddd:value` | Generate a value object | `php artisan ddd:value Shared:DollarAmount` |
| `ddd:view-model` | Generate a view model | `php artisan ddd:view-model Invoicing:ShowInvoiceViewModel` |
| `ddd:action` | Generate an action | `php artisan ddd:action Invoicing:SendInvoiceToCustomer` |
| `ddd:cast` | Generate a cast | `php artisan ddd:cast Invoicing:MoneyCast` |
| `ddd:channel` | Generate a channel | `php artisan ddd:channel Invoicing:InvoiceChannel` |
| `ddd:command` | Generate a command | `php artisan ddd:command Invoicing:InvoiceDeliver` |
| `ddd:controller` | Generate a controller | `php artisan ddd:controller Invoicing:InvoiceController`<br> <br>  Options: inherits options from *make:controller* |
| `ddd:event` | Generate an event | `php artisan ddd:event Invoicing:PaymentWasReceived` |
| `ddd:exception` | Generate an exception | `php artisan ddd:exception Invoicing:InvoiceNotFoundException` |
| `ddd:job` | Generate a job | `php artisan ddd:job Invoicing:GenerateInvoicePdf` |
| `ddd:listener` | Generate a listener | `php artisan ddd:listener Invoicing:HandlePaymentReceived` |
| `ddd:mail` | Generate a mail | `php artisan ddd:mail Invoicing:OverduePaymentReminderEmail` |
| `ddd:middleware` | Generate a middleware | `php artisan ddd:middleware Invoicing:VerifiedCustomerMiddleware` |
| `ddd:migration` | Generate a migration | `php artisan ddd:migration Invoicing:CreateInvoicesTable` |
| `ddd:notification` | Generate a notification | `php artisan ddd:notification Invoicing:YourPaymentWasReceived` |
| `ddd:observer` | Generate an observer | `php artisan ddd:observer Invoicing:InvoiceObserver` |
| `ddd:policy` | Generate a policy | `php artisan ddd:policy Invoicing:InvoicePolicy` |
| `ddd:provider` | Generate a provider | `php artisan ddd:provider Invoicing:InvoiceServiceProvider` |
| `ddd:resource` | Generate a resource | `php artisan ddd:resource Invoicing:InvoiceResource` |
| `ddd:rule` | Generate a rule | `php artisan ddd:rule Invoicing:ValidPaymentMethod` |
| `ddd:request` | Generate a form request | `php artisan ddd:request Invoicing:StoreInvoiceRequest` |
| `ddd:scope` | Generate a scope | `php artisan ddd:scope Invoicing:ArchivedInvoicesScope` |
| `ddd:seeder` | Generate a seeder | `php artisan ddd:seeder Invoicing:InvoiceSeeder` |
| `ddd:class` | Generate a class | `php artisan ddd:class Invoicing:Support/InvoiceBuilder` |
| `ddd:enum` | Generate an enum | `php artisan ddd:enum Customer:CustomerType` |
| `ddd:interface` | Generate an interface | `php artisan ddd:interface Customer:Contracts/Invoiceable` |
| `ddd:trait` | Generate a trait | `php artisan ddd:trait Customer:Concerns/HasInvoices` |

Generated objects will be placed in the appropriate domain namespace as specified by `ddd.namespaces.*` in the [config file](#config-file).

<a name="config-utility"></a>

### Config Utility (Since 1.2)
A configuration utility was introduced in 1.2 to help manage the package's configuration over time. 
```bash
php artisan ddd:config
```
Output:
```
 ┌ Laravel-DDD Config Utility ──────────────────────────────────┐
 │ › ● Run the configuration wizard                             │
 │   ○ Rebuild and merge ddd.php with latest package copy       │
 │   ○ Detect domain namespace from composer.json               │
 │   ○ Sync composer.json from ddd.php                          │
 │   ○ Exit                                                     │
 └──────────────────────────────────────────────────────────────┘
```
These config tasks are also invokeable directly using arguments:
```bash
# Run the configuration wizard
php artisan ddd:config wizard

# Rebuild and merge ddd.php with latest package copy
php artisan ddd:config update

# Detect domain namespace from composer.json
php artisan ddd:config detect

# Sync composer.json from ddd.php   
php artisan ddd:config composer
```

### Other Commands
```bash
# Show a summary of current domains in the domain folder
php artisan ddd:list

# Cache domain manifests (used for autoloading)
php artisan ddd:optimize

# Clear the domain cache
php artisan ddd:clear
```

## Advanced Usage
### Application Layer (since 1.2)
Some objects interact with the domain layer, but are not part of the domain layer themselves. By default, these include: `controller`, `request`, `middleware`. You may customize the path, namespace, and which `ddd:*` objects belong in the application layer.
```php
// In config/ddd.php
'application_path' => 'app/Modules',
'application_namespace' => 'App\Modules',
'application_objects' => [
    'controller',
    'request',
    'middleware',
],
```
The configuration above will result in the following:
```bash
ddd:model Invoicing:Invoice --controller --resource --requests
```
Output:
```
├─ app
|   └─ Modules
│       └─ Invoicing
│           ├─ Controllers
│           │   └─ InvoiceController.php
│           └─ Requests
│               ├─ StoreInvoiceRequest.php
│               └─ UpdateInvoiceRequest.php
├─ src/Domain
    └─ Invoicing
        └─ Models
            └─ Invoice.php
```

### Custom Layers (since 1.2)
Often times, additional top-level namespaces are needed to hold shared components, helpers, and things that are not domain-specific. A common example is the `Infrastructure` layer. You may configure these additional layers in the `ddd.layers` array.
```php
// In config/ddd.php
'layers' => [
    'Infrastructure' => 'src/Infrastructure',
],
```
The configuration above will result in the following:
```bash
ddd:model Invoicing:Invoice
ddd:trait Infrastructure:Concerns/HasExpiryDate
```
Output:
```
├─ src/Domain
|   └─ Invoicing
|       └─ Models
|           └─ Invoice.php
├─ src/Infrastructure
    └─ Concerns
        └─ HasExpiryDate.php
```
After defining new layers in `ddd.php`, make sure the corresponding namespaces are also registered in your `composer.json` file. You may use the `ddd:config` helper command to handle this for you.
```bash
# Sync composer.json with ddd.php
php artisan ddd:config composer
```

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

### Subdomains (nested domains)
Subdomains can be specified with dot notation wherever a domain option is accepted.
```bash
# Domain/Reporting/Internal/ViewModels/MonthlyInvoicesReportViewModel
php artisan ddd:view-model Reporting.Internal:MonthlyInvoicesReportViewModel

# Domain/Reporting/Customer/ViewModels/MonthlyInvoicesReportViewModel
php artisan ddd:view-model Reporting.Customer:MonthlyInvoicesReportViewModel

# (supported by all commands where a domain option is accepted)
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

### Custom Object Resolution
If you require advanced customization of generated object naming conventions, you may register a custom resolver using `DDD::resolveObjectSchemaUsing()` in your AppServiceProvider's boot method: 
```php
use Lunarstorm\LaravelDDD\Facades\DDD;
use Lunarstorm\LaravelDDD\ValueObjects\CommandContext;
use Lunarstorm\LaravelDDD\ValueObjects\ObjectSchema;

DDD::resolveObjectSchemaUsing(function (string $domainName, string $nameInput, string $type, CommandContext $command): ?ObjectSchema {
    if ($type === 'controller' && $command->option('api')) {
        return new ObjectSchema(
            name: $name = str($nameInput)->replaceEnd('Controller', '')->finish('ApiController')->toString(),
            namespace: "App\\Api\\Controllers\\{$domainName}",
            fullyQualifiedName: "App\\Api\\Controllers\\{$domainName}\\{$name}",
            path: "src/App/Api/Controllers/{$domainName}/{$name}.php",
        );
    }

    // Return null to fall back to the default
    return null;
});
```
The example above will result in the following:
```bash
php artisan ddd:controller Invoicing:PaymentController --api 
# Controller [src/App/Api/Controllers/Invoicing/PaymentApiController.php] created successfully. 
```

<a name="customizing-stubs"></a>

## Customizing Stubs
This package ships with a few ddd-specific stubs, while the rest are pulled from the framework. For a quick reference of available stubs and their source, you may use the `ddd:stub --list` command:
```bash
php artisan ddd:stub --list
```

### Stub Priority
When generating objects using `ddd:*`, stubs are prioritized as follows:
- Try `stubs/ddd/*.stub` (customized for `ddd:*` only)
- Try `stubs/*.stub` (shared by both `make:*` and `ddd:*`)
- Fallback to the package or framework default

### Publishing Stubs
To publish stubs interactively, you may use the `ddd:stub` command:
```bash
php artisan ddd:stub
```
```
 ┌ What do you want to do? ─────────────────────────────────────┐
 │ › ● Choose stubs to publish                                  │
 │   ○ Publish all stubs                                        │
 └──────────────────────────────────────────────────────────────┘

 ┌ Which stub should be published? ─────────────────────────────┐
 │ policy                                                       │
 ├──────────────────────────────────────────────────────────────┤
 │ › ◼ policy.plain.stub                                        │
 │   ◻ policy.stub                                              │
 └────────────────────────────────────────────────── 1 selected ┘
  Use the space bar to select options.
```
You may also use shortcuts to skip the interactive steps:
```bash
# Publish all stubs
php artisan ddd:stub --all

# Publish one or more stubs specified as arguments (see ddd:stub --list)
php artisan ddd:stub model
php artisan ddd:stub model dto action
php artisan ddd:stub controller controller.plain controller.api

# Options:

# Publish and overwrite only the files that have already been published
php artisan ddd:stub ... --existing

# Overwrite any existing files
php artisan ddd:stub ... --force
```
To publish multiple stubs with common prefixes at once, use `*` or `.` as a wildcard ending to indicate "stubs that starts with":
```bash 
php artisan ddd:stub listener.
```
Output:
```bash
Publishing /stubs/ddd/listener.typed.queued.stub
Publishing /stubs/ddd/listener.queued.stub
Publishing /stubs/ddd/listener.typed.stub
Publishing /stubs/ddd/listener.stub
```

## Domain Autoloading and Discovery
Autoloading behaviour can be configured with the `ddd.autoload` configuration option. By default, domain providers, commands, policies, and factories are auto-discovered and registered.

```php
'autoload' => [
    'providers' => true,
    'commands' => true,
    'policies' => true,
    'factories' => true,
    'migrations' => true,
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

### Migrations
When `ddd.autoload.migrations` is enabled, paths within the domain layer matching the configured `ddd.namespaces.migration` namespace will be auto-registered as a database migration path and recognized by `php artisan migrate`.

### Ignoring Paths During Autoloading
To specify folders or paths that should be skipped during autoloading class discovery, add them to the `ddd.autoload_ignore` configuration option. By default, the `Tests` and `Migrations` folders are ignored.
```php
'autoload_ignore' => [
    'Tests',
    'Database/Migrations',
],
```
Note that ignoring folders only applies to class-based autoloading: Service Providers, Console Commands, Policies, and Factories.

Paths specified here are relative to the root of each domain. e.g., `src/Domain/Invoicing/{path-to-ignore}`. If more advanced filtering is needed, a callback can be registered using `DDD::filterAutoloadPathsUsing(callback $filter)` in your AppServiceProvider's boot method:
```php
use Lunarstorm\LaravelDDD\Facades\DDD;
use Symfony\Component\Finder\SplFileInfo;

DDD::filterAutoloadPathsUsing(function (SplFileInfo $file) {
    if (basename($file->getRelativePathname()) === 'functions.php') {
        return false;
    }
});
```
The filter callback is based on Symfony's [Finder Component](https://symfony.com/doc/current/components/finder.html#custom-filtering).

### Disabling Autoloading
You may disable autoloading by setting the respective autoload options to `false` in the configuration file as needed, or by commenting out the autoload configuration entirely.
```php
// 'autoload' => [
//     'providers' => true,
//     'commands' => true,
//     'policies' => true,
//     'factories' => true,
//     'migrations' => true,
// ],
```

<a name="autoloading-in-production"></a>

## Autoloading in Production
In production, you should cache the autoload manifests using the `ddd:optimize` command as part of your application's deployment process. This will speed up the auto-discovery and registration of domain providers and commands. The `ddd:clear` command may be used to clear the cache if needed. If you are already running `php artisan optimize`, `ddd:optimize` will be included within that pipeline. The framework's `optimize` and `optimize:clear` commands will automatically invoke `ddd:optimize` and `ddd:clear` respectively.

<a name="config-file"></a>

## Configuration File
This is the content of the published config file (`ddd.php`):

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Domain Layer
    |--------------------------------------------------------------------------
    |
    | The path and namespace of the domain layer.
    |
    */
    'domain_path' => 'src/Domain',
    'domain_namespace' => 'Domain',

    /*
    |--------------------------------------------------------------------------
    | Application Layer
    |--------------------------------------------------------------------------
    |
    | The path and namespace of the application layer, and the objects
    | that should be recognized as part of the application layer.
    |
    */
    'application_path' => 'app/Modules',
    'application_namespace' => 'App\Modules',
    'application_objects' => [
        'controller',
        'request',
        'middleware',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Layers
    |--------------------------------------------------------------------------
    |
    | Additional top-level namespaces and paths that should be recognized as
    | layers when generating ddd:* objects.
    |
    | e.g., 'Infrastructure' => 'src/Infrastructure',
    |
    */
    'layers' => [
        'Infrastructure' => 'src/Infrastructure',
        // 'Integrations' => 'src/Integrations',
        // 'Support' => 'src/Support',
    ],

    /*
    |--------------------------------------------------------------------------
    | Object Namespaces
    |--------------------------------------------------------------------------
    |
    | This value contains the default namespaces of ddd:* generated
    | objects relative to the layer of which the object belongs to.
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
        'controller' => 'Controllers',
        'enum' => 'Enums',
        'event' => 'Events',
        'exception' => 'Exceptions',
        'factory' => 'Database\Factories',
        'interface' => '',
        'job' => 'Jobs',
        'listener' => 'Listeners',
        'mail' => 'Mail',
        'middleware' => 'Middleware',
        'migration' => 'Database\Migrations',
        'notification' => 'Notifications',
        'observer' => 'Observers',
        'policy' => 'Policies',
        'provider' => 'Providers',
        'resource' => 'Resources',
        'request' => 'Requests',
        'rule' => 'Rules',
        'scope' => 'Scopes',
        'seeder' => 'Database\Seeders',
        'trait' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Base Model
    |--------------------------------------------------------------------------
    |
    | The base model class which generated domain models should extend. If
    | set to null, the generated models will extend Laravel's default.
    |
    */
    'base_model' => null,

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
    | Configure whether domain providers, commands, policies, factories,
    | and migrations should be auto-discovered and registered.
    |
    */
    'autoload' => [
        'providers' => true,
        'commands' => true,
        'policies' => true,
        'factories' => true,
        'migrations' => true,
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
    | using `DDD::filterAutoloadPathsUsing(callback $filter)` in
    | the AppServiceProvider's boot method.
    |
    */
    'autoload_ignore' => [
        'Tests',
        'Database/Migrations',
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
