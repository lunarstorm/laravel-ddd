# Changelog

All notable changes to `laravel-ddd` will be documented in this file.

## [0.10.0] - 2024-03-23
### Added
- Add `ddd.domain_path` and `ddd.domain_namespace` to config, to specify the path to the domain layer and root domain namespace more explicitly (replaces the previous `ddd.paths.domains` config).
- Implement `Lunarstorm\LaravelDDD\Factories\HasDomainFactory` trait which can be used on domain models that are unable to extend the base domain model.

### Changed
- Default `base-model.php.stub` now utilizes the `HasDomainFactory` trait.

### Deprecated
- Config `ddd.paths.domains` deprecated in favour of `ddd.domain_path` and `ddd.domain_namespace`. Existing config files published prior to this release should remove `ddd.paths.domains` and add `ddd.domain_path` and `ddd.domain_namespace` accordingly.

## [0.9.0] - 2024-03-11
### Changed
- Internals: normalize generator file paths using `DIRECTORY_SEPARATOR` for consistency across different operating systems when it comes to console output and test expectations.

### Chore
- Add Laravel 11 support.
- Add PHP 8.3 support.

## [0.8.1] - 2023-12-05
### Chore
- Update dependencies.

## [0.8.0] - 2023-11-12
### Changed
- Implement proper support for custom base models when using `ddd:model`:
    - If the configured `ddd.base_model` exists (evaluated using `class_exists`), base model generation is skipped.
    - If `ddd.base_model` does not exist and falls under a domain namespace, base model will be generated.
    - Falling under a domain namespace means `Domain\**\Models\**`.
    - If `ddd.base_model` were set to `App\Models\NonExistentModel` or `Illuminate\Database\Eloquent\NonExistentModel`, they fall outside of the domain namespace and will not be generated for you.

### Fixed
- Resolve long-standing issue where `ddd:model` would not properly detect whether the configured `ddd.base_model` already exists, leading to unintended results.

### Chore
- Update composer dependencies.

### BREAKING
- The default domain model stub `model.php.stub` has changed. If stubs were published prior to this release, you may have to delete and re-publish; unless the published `model.php.stub` has been entirely customized with independent logic for your respective application.

## [0.7.0] - 2023-10-22
### Added
- Formal support for subdomains (nested domains). For example, to generate model `Domain\Reporting\Internal\Models\InvoiceReport`, the domain argument can be specified with dot notation: `ddd:model Reporting.Internal InvoiceReport`. Specifying `Reporting/Internal` or `Reporting\\Internal` will also be accepted and normalized to dot notation internally.
- Implement abstract `Lunarstorm\LaravelDDD\Factories\DomainFactory` extension of `Illuminate\Database\Eloquent\Factories\Factory`:
    - Implements `DomainFactory::resolveFactoryName()` to resolve the corresponding factory for a domain model.
    - Will resolve the correct factory if the model belongs to a subdomain; `Domain\Reporting\Internal\Models\InvoiceReport` will correctly resolve to `Database\Factories\Reporting\Internal\InvoiceReportFactory`.

### Changed
- Default base model implementation in `base-model.php.stub` now uses `DomainFactory::factoryForModel()` inside the `newFactory` method to resolve the model factory.

### BREAKING
- For existing installations of the package to support sub-domain model factories, the base model's `newFactory()` should be updated where applicable; see `base-model.php.stub`.
```php
use Lunarstorm\LaravelDDD\Factories\DomainFactory;

// ...

protected static function newFactory()
{
    return DomainFactory::factoryForModel(get_called_class());
}
```

## [0.6.1] - 2023-08-14
### Fixed
- Ensure generated domain factories set the `protected $model` property.
- Ensure generated factory classes are always suffixed by `Factory`.

## [0.6.0] - 2023-08-14
### Added
- Ability to generate domain model factories, in a few ways:
    - `ddd:factory Invoicing InvoiceFactory`
    - `ddd:model Invoicing Invoice --factory`
    - `ddd:model Invoicing Invoice -f`
    - `ddd:model -f` (if relying on prompts)

## [0.5.1] - 2023-06-27
### Changed
- Clean up default stubs; get rid of extraneous ellipses in comment blocks and ensure code style is consistent.

### Fixed
- Ensure generator commands show a nicely sanitized path to generated file in the console output (previously, double slashes were present). Only applies to Laravel 9.32.0 onwards, when file paths were added to the console output.

### Chore
- Upgrade test suite to use Pest 2.x.

## [0.5.0] - 2023-06-14
### Added
- Ability to generate actions (`ddd:action`), which by default generates an action class based on the `lorisleiva/laravel-actions` package.

### Changed
- Minor cleanups and updates to the default `ddd.php` config file.
- Update stubs to be less opinionated where possible.

## [0.4.0] - 2023-05-08
### Changed
- Update argument definitions across generator commands to play nicely with `PromptsForMissingInput` behaviour introduced in Laravel v9.49.0.

### Fixed
- Ensure the configured domain path and namespace is respected by `ddd:base-model` and `ddd:base-view-model`.

## [0.3.0] - 2023-03-23
### Added
- Increase test coverage to ensure expected namespaces are present in generated objects.

### Changed
- Domain generator commands will infer the root domain namespace based on the configured `ddd.paths.domains`.
- Change the default domain path in config to `src/Domain` (was previously `src/Domains`), thereby generating objects with the singular `Domain` root namespace.

## [0.2.0] - 2023-03-20
### Added
- Support for Laravel 10.

### Changed
- Install command now publishes config, registers the default domain path in composer.json, and prompts to publish stubs.
- Generator command signatures simplified to `ddd:*` (previously `ddd:make:*`).

### Fixed
- When ViewModels are generated, base view model import statement is now properly substituted.

## [0.1.0] - 2023-01-19
### Added
- Early version of generator commands for domain models, dto, value objects, view models.
- `ddd:install` command to automatically register the domain folder inside the application's composer.json (experimental)
