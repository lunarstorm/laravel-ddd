# Changelog

All notable changes to `laravel-ddd` will be documented in this file.

## [Unversioned]
### Changed
- Clean up default stubs; get rid of extraneous ellipses in comment blocks and ensure code style is consistent.

### Fixed
- Ensure generator commands show a nicely sanitized path to generated file in the console output (previously, double slashes were present).

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
